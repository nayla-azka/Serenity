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
            // Validate NIS and password
            $request->validate([
                'nis' => 'required|string',
                'password' => 'required',
            ]);

            // Find student by NIS
            $student = Student::where('nis', $request->nis)->first();

            if (!$student) {
                throw new \Exception('NIS tidak ditemukan.');
            }

            // Get the associated user
            $user = $student->user()->first();

            if (!$user) {
                throw new \Exception('Akun tidak terhubung dengan data siswa.');
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                throw new \Exception('NIS atau password salah.');
            }

            // Login successful
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