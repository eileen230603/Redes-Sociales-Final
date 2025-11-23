<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function publicHome()
    {
        return view('home-publica');
    }

    public function redirectByRole()
    {
        $user = Auth::user();

        if ($user->esOng()) {
            return view('home-ong', compact('user'));
        }

        return view('home-publica', compact('user'));
    }
}
