<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo_electronico' => 'required|email',
            'contrasena' => 'required',
        ]);

        $user = User::where('correo_electronico', $request->correo_electronico)->first();

        if (!$user || !Hash::check($request->contrasena, $user->contrasena)) {
            return back()->withErrors(['correo_electronico' => 'Credenciales incorrectas.']);
        }

        if (!$user->activo) {
            return back()->withErrors(['correo_electronico' => 'Cuenta inactiva.']);
        }

        Auth::login($user);

        return redirect()->route('home');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home.publica');
    }
}
