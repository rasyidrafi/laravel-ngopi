<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Placeholder
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function placeholder(Request $request)
    {
        //
    }

    /**
     * register
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "username" => "required|unique:users",
            "password" => "required|min:6",
            "role" => "required|in:kasir,manajer,admin",
        ]);

        $newUser = User::create($request->all());
        $newUser->password = bcrypt($request->password);
        $newUser->created_by = $request->user->id;
        $newUser->token = md5($newUser->username);
        $newUser->save();

        return response()->json([
            "status" => "success",
            "message" => "User created successfully",
            "data" => User::find($newUser->id)
        ]);
    }

    /**
     * login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            "username" => "required",
            "password" => "required|min:6"
        ]);

        $user = User::where("username", $request->username)->first();
        if (!$user) return response()->json([
            "status" => "error",
            "message" => "User or password is incorrect"
        ], 401);

        $isValid = Hash::check($request->password, $user->password);
        if (!$isValid) return response()->json([
            "status" => "error",
            "message" => "User or password is incorrect"
        ], 401);

        $user->is_login = true;
        $user->save();

        return response()->json([
            "status" => "success",
            "message" => "Login success",
            "data" => $user
        ]);
    }
}
