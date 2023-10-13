<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\SuccessResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->Validated());
        $user->assignRole('user');
        return $user;
    }
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            Auth::user()->tokens()->delete();
            return SuccessResource::make([
                'token' => Auth::user()->createToken(Auth::user()->name)->plainTextToken,
                'user'  => Auth::user()
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        return $request->user()->tokens()->delete();
    }
}
