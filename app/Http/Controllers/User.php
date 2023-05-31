<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Controller
{
    public function register(Request $request) : JsonResponse {
        if (empty($request->username)) {
            return new JsonResponse([
                "ErrorCode" => -1,
                "ErrorMessage" => "Username Missing"
            ]);
        } else if (empty($request->password)) {
            return new JsonResponse([
                "ErrorCode" => -1,
                "ErrorMessage" => "Password Missing"
            ]);
        }

        $user = new Users;

        $usernameExist = $user::where('username', $request->username)->first();
        if ($usernameExist) {
            return new JsonResponse([
                "ErrorCode" => -1,
                "ErrorMessage" => "Username Exists"
            ]);
        }

        $user->username = $request->username;
        $user->password = Hash::make($request->password, [
            'rounds' => 12,
        ]);;

        $user->saveOrFail();

        return new JsonResponse([
            "ErrorCode" => 0,
            "ErrorMessage" => "Registered Successfully"
        ]);
    }

    public function login(Request $request) : JsonResponse {
        if (empty($request->username)) {
            return new JsonResponse([
                "ErrorCode" => -1,
                "ErrorMessage" => "Username Missing"
            ]);
        } else if (empty($request->password)) {
            return new JsonResponse([
                "ErrorCode" => -1,
                "ErrorMessage" => "Password Missing"
            ]);
        }

        $user = new Users;

        $userFound = $user::where('username', $request->username)->first();
        if (!$userFound) {
            return new JsonResponse([
                "ErrorCode" => -1,
                "ErrorMessage" => "User not found"
            ]);
        }

        $isPasswordEqual = Hash::check($request->password, $userFound->password);
        if (!$isPasswordEqual) {
            return new JsonResponse([
                "ErrorCode" => -1,
                "ErrorMessage" => "Wrong password"
            ]);
        }

        $accessToken = $this->generateAccessToken();
        $this->saveAccessToken($user, $accessToken, $userFound->id);

        $response = new JsonResponse([
            "ErrorCode" => 0,
            "ErrorMessage" => "Authorized Successfully"
        ]);
        $response->withCookie(Cookie::make('access_token', $accessToken['token'], 60 * 24, null, null, false, true)); // Setting HTTP-only cookie with a 60-minute expiration

        return $response;
    }

    private function generateAccessToken() : array {
        $expiration = Carbon::now()->addHours(24)->timestamp;
        return [
            'token' => Str::random(32),
            'expiration' => $expiration
        ];
    }

    private function saveAccessToken(Users $users, array $token, int $userId) : bool {
        $user = $users::find($userId);
        if (!$user) {
            return false;
        }

        $user->token = $token['token'];
        $user->token_expiration = $token['expiration'];

        $user->saveOrFail();

        return true;
    }

    public function isTokenValid($token) : bool {
        $user = new Users;

        $currentTimestamp = Carbon::now()->timestamp;
        $token = $user::where('token', $token)->where('token_expiration', '>', $currentTimestamp)->first();

        if ($token) {
            return true;
        }

        return false;
    }
}
