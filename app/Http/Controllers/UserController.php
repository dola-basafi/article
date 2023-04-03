<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'unique:users', 'email'],
            'password' => ['required', 'min:6'],
        ],
        [
            'required' => ':attribute cannot empty',
            'unique' => ':attribute has been used',
            'min' => ':attribute minimal 6 character'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "error" => $validator->errors()], 400);
        }
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        return response()->json(([
            "success" => true,
            "messages" =>"success created user"
        ]));
    }

    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'email:dns'],
            'password' => ['required', 'min:6']
        ],[
            'required' => ':attribute cannot empty',
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "error" => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'messages' => 'Login Failed, The data you entered is incorrect!!'
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'messages' => 'Login success',
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
    }

    function logout(Request $request)
    {
        $user = $request->user();
        // deletes all tokens based on user login
        $token = $user->tokens()->where('tokenable_id', $user->id)->delete();
        return response()->json([
            'success'           => true,
            'massage'           => 'Logout Successfully',
            'tokenOnDeleted'    => $token,
        ], 200);
    }

}
