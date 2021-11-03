<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $credentials =  $request->only('email', 'password');
        $data = $request->all();
        if(Auth::attempt($credentials)) {
            $user = User::where("email", $data['email'])->first();
            $token = $user->createToken('joblisting')->accessToken;
            return response()->json([
                "success" => true,
                "user" => $user,
                "token" => $token
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email/Password is incorrect'
            ], 500);
        }
    }

    public function register(Request $request) {
        $validation = Validator::make($request->all(),[
            'email' => 'required|string|email|unique:users'
        ]);
        $data = $request->all();
        if($validation->fails()){
            $errors = $validation->errors();
            return response()->json([
                'success' => false,
                'message' => $errors
            ], 500);
        } else{
            User::create([
                'name' => $data['business_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password'])
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Registered sucessfully'
            ] , 201);
        }
    }
}
