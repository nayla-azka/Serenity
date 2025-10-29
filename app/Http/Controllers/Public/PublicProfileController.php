<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class PublicProfileController extends Controller
{
    public function index()
    {
        $student = Student::with('class')
        ->where('user_id', Auth::id())
        ->first();

        return view('public.profile', compact('student'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/'); // redirect ke halaman utama/login
    }
}
