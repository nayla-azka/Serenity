<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Session;

class PublicLoginController extends PublicBaseController
{
    public function showLogin()
    {
        return view('public.login');
    }

    public function login(Request $request)
    {
        return $this->tryCatchResponse(function () use ($request) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Cari user berdasarkan email
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new \Exception('Email atau password salah.');
            }

            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                throw new \Exception('Akun tidak terhubung dengan data siswa.');
            }

            // Setelah validasi sukses
            Auth::login($user);
            $request->session()->regenerate();
        },
        'Login berhasil!',
        'Gagal login.',
        'public.index'
        );
    }

    public function profile()
    {
        return view('public.profile');
    }
}
