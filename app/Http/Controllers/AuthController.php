<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginUserRequest;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {

        $request->validated($request->all());

        if (!Auth::attempt(
            [
                'email' => $request->email,
                'password' => $request->password,
            ]
        )) {
            return $this->error('', 'Credentials do not match',  401,);
        }

        $user = User::where('email', $request->email)->first();
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token of' . $user->name)->plainTextToken,
        ]);
    }


    public function register(StoreUserRequest $request)
    {

        // dd($request);

        $request->validated($request->all());

        // dd($request->all());


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return $this->success([
            'User' => $user,
            'token' => $user->createToken('Api Token of' . $user->name)->plainTextToken,
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'message' => 'Logged out successfully'
        ]);
    }
}
