<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Login user and create token
     * 
     * @param [string] email
     * @param [string] password
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password'  => 'required',
            'remember_me'   => 'boolean'
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials))
            return response()->json([
                'message'   => 'Unauthorized'
            ], 401);
        
            $user = $request->user();
            
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;

            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);

            $token->save();

            return response()->json([
                'access_token'  => $tokenResult->accessToken,
                'token_type'    => 'Bearer',
                'expires_at'    => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
    }

    /**
     * Logout user (Revoke the token)
     * 
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message'   => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     * 
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
