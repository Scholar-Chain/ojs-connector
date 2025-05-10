<?php

namespace App\Http\Controllers\OJS;

use App\Http\Controllers\Controller;
use App\Models\OJS\Journal;
use App\Repositories\Eloquent\OJS\JournalRepository;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    private $journalModel;

    public function __construct(JournalRepository $journalRepository)
    {
        $this->journalModel = $journalRepository;
    }

    public function index(Request $request)
    {
        try {
            return $this->journalModel->all($request->all());
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

    public function show($id)
    {
        try {
            $data = $this->journalModel->find($id);
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
}
