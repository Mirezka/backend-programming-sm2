<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|string',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        $token = $user->createToken('ayyashMuhammadToken')->plainTextToken;

        $response =[
            'message'=>'Succesfully created a new user. Dont be a stranger, ' . $user . '!',
            'user'=>$user,
            'token'=>$token
        ];

        return response()->json($response, 201);
    }

    public function logout() {
        auth()->user()->tokens()->delete();
        $message = ['message'=>'Succesfully logged out.'];
        return response()->json($message, 200);
    }

    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'required|email|max:191',
            'password' => 'required|string',
        ]);
        
        $user = User::where('email', $data['email'])->first();
        
        if(!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message'=>'Invalid username or password, please try again.'], 401);
        }
        else {
            $token = $user->createToken('ayyashMuhammadTokenLogin')->plainTextToken;
            $response = [
                'message'=>'Logged in successfully, welcome back, ' . $user->name . '!',
                'user' => $user,
                'token' => $token
            ];

            return response()->json($response, 200);
        }
    }
}
