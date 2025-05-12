<?php

namespace App\Http\Controllers\OJS;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\OJS\UserSetting;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\OJS\User as OJSUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Register as RegisterRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        DB::connection('ojs')->beginTransaction();
        try {
            $data = $request->validated();
            $ojsUser = OJSUser::create([
                "username" => $data['username'],
                "password" => bcrypt($data['password']),
                "email" => $data['email'],
                "country" => "ID",
                "date_registered" => now(),
                "date_last_login" => now(),
                "inline_help" => 1,
            ]);

            $user = User::create([
                "name" => $data['given_name'],
                "email" => $data['email'],
                "password" => bcrypt($data['password']),
                "ojs_user_id" => $ojsUser->user_id,
            ]);

            $token = JWTAuth::fromUser($user);
            $user->update(['access_token' => $token]);


            collect(['affiliation', 'givenName', 'familyName'])->each(function ($setting) use ($ojsUser, $data) {
                UserSetting::create([
                    'user_id' => $ojsUser->user_id,
                    'setting_name' => $setting,
                    'setting_value' => $data[Str::snake($setting)],
                    'setting_type' => 'string',
                    'locale' => 'en_US',
                ]);
            });
            DB::commit();
            DB::connection('ojs')->commit();
            return response()->json(['status' => 200, 'data' => new UserResource($user, true)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::connection('ojs')->rollback();
            DB::rollback();
            return response()->json([
                'errors' => 'Data not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::connection('ojs')->rollback();
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);
            DB::connection('ojs')->rollback();
            DB::rollback();
            return response()->json([
                'errors' => 'Proses data gagal, silahkan coba lagi',
            ], $e->getCode() == 0 ? 500 : ($e->getCode() != 23000 ? $e->getCode() : 500));
        }
    }
}
