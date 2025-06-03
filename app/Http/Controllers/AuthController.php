<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $usuario = User::select('id', 'username')->findOrFail($user->id);

                $rol_id = $user->roles->first()->id;
                $usuario->rol = $rol_id;

                return response()->json(['success'=>true,'user' => $usuario], 200);
            } else {
                return response()->json(['false'=>true,'error' => 'Unauthorized'], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
