<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response([
                'message' => "Provided Username or Password is incorrect.",
                'status' => "warning",
                'errors' => [
                    'username' => 'The provided credentials do not match our records.',
                    ]
            ],422);
        }

        // $request->session()->regenerate();
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;

        return response(compact('user','token'));
    }
    

    public function logout()
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = $req->user();
            $user->currentAccessToken()->delete;
            Auth::logout();

            return response('',204);
        }
    }

    public function username()
    {
        return 'username';
    }
}
