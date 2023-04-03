<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
            'emai' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
    }

}
