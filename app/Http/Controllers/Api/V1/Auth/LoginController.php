<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !\Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $deviceName = substr($request->header('User-Agent') ?? 'Unknown', 0, 255);
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
