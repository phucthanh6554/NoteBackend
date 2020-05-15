<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use \Firebase\JWT\JWT;

use App\User;

class Users extends Controller
{
    //
    public function create(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'password' => 'required',
            'email' => 'required | unique:users'
        ]);

        $user = new User();
        $user->fill($request->all());

        if($user->save())
        {
            $token = $this->makeJWTToken($user);
            return response()->json(['status' => 'Ok', 'token' => $token]);
        }
        else
            return response()->json(['status' => 'Error']);
    }

    public function login(Request $request)
    {
        if($request->has('email') && $request->has('password'))
        {
            $user = User::where('email', $request->email)->first();

            // Login successfully
            if($user != null && Hash::check($request->password, $user->password))
            {
                $token = $this->makeJWTToken($user);
                return response()->json(['status' => 'Ok', 'token' => $token]);
            }else{
                return response()->json(['status' => 'Error'], 403);
            }
        }
    }

    public function changePassword(Request $request)
    {
        $validateData = $request->validate([
            'email' => 'required',
            'password' => 'required',
            'new_password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        // Correct password
        if($user != null && Hash::check($request->password, $user->password))
        {
            $user->password = $request->new_password;
            if($user->save())
                return response()->json(['status' => 'Ok']);
            else
                return response()->json(['status' => 'Save error']);
        }else{
            return response()->json(['status' => 'Error'], 403);
        }
    }

    private function makeJWTToken($user)
    {
        $key = env('JWT_KEY');
        $exp = env('JWT_EXP');
        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'exp' => strtotime("+$exp hours")
        ];

        return JWT::encode($payload, $key);
    }
}
