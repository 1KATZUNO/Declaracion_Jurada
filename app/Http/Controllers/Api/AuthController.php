<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $req->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6'
        ]);

        $user = User::create([
            'name'=>$req->name,
            'email'=>$req->email,
            'password'=>Hash::make($req->password),
            'role'=>$req->role ?? 'profesor',
            'cedula'=>$req->cedula ?? null,
            'departamento'=>$req->departamento ?? null,
            'telefono'=>$req->telefono ?? null,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user'=>$user,'token'=>$token],201);
    }

    public function login(Request $req)
    {
        $credentials = $req->only('email','password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['message'=>'Credenciales inválidas'], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['user'=>$user,'token'=>$token]);
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Sesión cerrada']);
    }
}
