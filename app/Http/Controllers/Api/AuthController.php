<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return \handleApiRequest(function () use ($request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ]);

            $user = User::create([ 
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'password' => Hash::make($request->post('password')),
            ]);

            $this->response['msg'] = 'registered successfully';
            $this->response['data'] = $user;

            return response()->json($this->response);
        });
    }

    public function login(Request $request)
    {
        return \handleApiRequest(function () use ($request) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->post('email'))->first();

            if (!$user || !Hash::check($request->post('password'), $user->password)) {
                throw new ApiStatusZeroException('invalid email or password');
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $this->response['msg'] = 'login successfully';
            $this->response['data'] = [
                'user' => $user,
                'token' => $token,
            ];

            return response()->json($this->response);
        });
    }

    public function logout(Request $request)
    {
        return handleApiRequest(function () use ($request){

        $request->user()->currentAccessToken()->delete();

        $this->response['msg'] = 'logout successfully';
        $this->response['data'] = [];

        return response()->json($this->response);
        });
    }

    public function profile(Request $request)
    {
        return handleApiRequest(function () use ($request){

        $this->response['msg'] = 'user profile';
        $this->response['data'] = $request->user();

        return response()->json($this->response);
        
        });
    }
}
