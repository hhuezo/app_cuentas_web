<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $usuario = User::select('id','username')->findOrFail($user->id);

            $rol_id = $user->roles->first()->id;
            $usuario->rol = $rol_id;
            //$token = $user->createToken('AuthToken')->accessToken;
            return response()->json(['user' => $usuario], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
