<?php

namespace App\Http\Controllers\OJS;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\Create as SubmissionCreate;
use App\Http\Requests\Submission\Show as ShowRequest;
use App\Models\OJS\Author;
use App\Models\OJS\AuthorSetting;
use App\Models\OJS\ControlledVocab;
use App\Models\OJS\EventLog;
use App\Models\OJS\Journal;
use App\Models\OJS\Publication;
use App\Models\OJS\PublicationSetting;
use App\Models\OJS\StageAssignment;
use App\Models\OJS\Submission;
use App\Models\OJS\UserGroup;
use App\Repositories\Eloquent\OJS\SubmissionRepository;

class SubmissionController extends Controller
{
    private $submissionModel;

    public function __construct(SubmissionRepository $submissionRepository)
    {
        $this->submissionModel = $submissionRepository;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->ojsUser()->id;

        try {
            return $this->submissionModel->all($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'errors' => 'Data not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'errors' => 'Data process failed, please try again',
            ], $e->getCode() == 0 ? 500 : ($e->getCode() != 23000 ? $e->getCode() : 500));
        }
    }

    public function show(ShowRequest $request, $id)
    {
        try {
            $data = $this->submissionModel->find($id);
            return response()->json($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'errors' => 'Data not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'errors' => 'Data process failed, please try again',
            ], $e->getCode() == 0 ? 500 : ($e->getCode() != 23000 ? $e->getCode() : 500));
        }
    }

    public function store(Journal $journal)
    {
        DB::beginTransaction();
        DB::connection('ojs')->beginTransaction();
        try {
            $ojsUser = auth()->user()->ojsUser();
            $userGroup = UserGroup::where('role_id', 65536)
                ->where('context_id', $journal->journal_id)
                ->first();

            $userGroup->users()->sync([
                'user_id' => $ojsUser->user_id
            ]);

            $submission = Submission::create([
                "context_id" => $journal->journal_id,
                "date_last_activity" => now(),
                "last_modified" => now(),
                "locale" => "en_US",
                "stage_id" => 1,
                "submission_progress" => 2
            ]);

            $publication = Publication::create([
                "last_modified" => now(),
                "section_id" => $journal->journal_id,
                "submission_id" => $submission->submission_id,
                "status" => 1,
                "version" => 1
            ]);

            DB::connection('ojs')->table('publication_settings')->insert([
                "publication_id" => $publication->publication_id,
                "locale" => "",
                "setting_name" => "categoryIds",
                "setting_value" => "[]"
            ]);

            collect([
                "submissionKeyword",
                "submissionSubject",
                "submissionDiscipline",
                "submissionLanguage",
                "submissionAgency",
            ])->each(function ($value) use ($publication) {
                ControlledVocab::create([
                    "symbolic" => $value,
                    "assoc_type" => 1048588,
                    "assoc_id" => $publication->publication_id
                ]);
            });

            $submission->update(['current_publication_id' => $publication->publication_id]);

            DB::connection('ojs')->table('submission_settings')
                ->where('submission_id', $submission->submission_id)
                ->whereIn('setting_name', ['_href', 'publications', 'reviewRounds', 'reviewAssignments', 'stages', 'statusLabel', 'urlAuthorWorkflow', 'urlEditorialWorkflow', 'urlWorkflow', 'urlPublished'])
                ->delete();

            $author = Author::create([
                "email" => $ojsUser->email,
                "include_in_browse" => 1,
                "publication_id" => $publication->publication_id,
                "user_group_id" => $userGroup->user_group_id
            ]);

            DB::connection('ojs')->table('author_settings')->insert([
                'author_id' => $author->author_id,
                'locale' => "ID",
                'setting_name' => "country",
                'setting_value' => "ID",
            ]);

            collect([
                'affiliation',
                'familyName',
                'givenName',
            ])->each(function ($value) use ($author, $ojsUser) {
                $setting = $ojsUser->userSetting($value)->first();

                DB::connection('ojs')->table('author_settings')->insert([
                    'author_id' => $author->author_id,
                    'locale' => "ID",
                    'setting_name' => $value,
                    'setting_value' => $setting->setting_value,
                ]);
            });

            $publication->update(['primary_contact_id' => $author->author_id]);

            EventLog::create([
                "user_id" => $ojsUser->user_id,
                "date_logged" => now(),
                "event_type" => 268435458,
                "assoc_type" => 1048585,
                "assoc_id" => $publication->publication_id,
                "message" => "submission.event.general.metadataUpdated",
                "is_translated" => 0
            ]);

            StageAssignment::create([
                "submission_id" => $submission->submission_id,
                "user_group_id" => $userGroup->user_group_id,
                "user_id" => $ojsUser->user_id,
                "date_assigned" => now(),
                "recommend_only" => 0,
                "can_change_metadata" => 0
            ]);
            DB::commit();
            DB::connection('ojs')->commit();
            return response()->json($this->submissionModel->find($submission->submission_id));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollback();
            DB::connection('ojs')->rollBack();
            return response()->json([
                'errors' => 'Data not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            DB::connection('ojs')->rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            DB::connection('ojs')->rollBack();
            report($e);
            return response()->json([
                'errors' => 'Data process failed, please try again',
            ], $e->getCode() == 0 ? 500 : ($e->getCode() != 23000 ? $e->getCode() : 500));
        }
    }
}
