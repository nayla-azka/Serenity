<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends AdminBaseController
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        return $this->tryCatchResponse(function () use ($request) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $credentials = $request->only('email', 'password');
            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                // throw exception supaya masuk ke catch
                throw new \Exception('Invalid email or password.');
            }

            if (!in_array($user->role, ['admin', 'konselor'])) {
                throw new \Exception('Access denied.');
            }

            Auth::login($user);
            $request->session()->regenerate();
        },
        'Login berhasil!',
        'Gagal login.',
        'admin.dashboard'
        );
    }


    public function dashboard()
    {
        return view('admin.dashboard'); 
    }
}
