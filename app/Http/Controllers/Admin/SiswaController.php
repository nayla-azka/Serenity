<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\SiswaDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Student as Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SiswaController extends AdminBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(SiswaDataTable $siswaDataTable)
    {
        return $siswaDataTable->render('admin.siswa.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('admin.siswa.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->tryCatchResponse(function () use ($request) {
            $request->validate([
                'nisn' => 'required|unique:student,nisn',
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'student_name' => 'required',
                'class_id' => 'required',
            ]);

            // Create user
            $user = User::create([
                'name' => $request->student_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa',
            ]);

            $path = $request->hasFile('photo')
                ? $request->file('photo')->store('siswa', 'public')
                : 'default.jpg';

            // Create student profile
            Siswa::create([
                'nisn' => $request->nisn,
                'student_name' => $request->student_name,
                'photo' => $path,
                'class_id' => $request->class_id,
                'user_id' => $user->id,
            ]);
        },
        'Data siswa berhasil ditambah!',
        'Gagal menambah siswa.',
        'admin.siswa.index'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kelas = Kelas::all();
        $siswa = Siswa::with('user')->findOrFail($id);
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        return $this->tryCatchResponse(function () use ($request, $siswa) {
            $user = $siswa->user;

            $request->validate([
                'nisn' => 'required|unique:student,nisn,' . $siswa->id_student . ',id_student',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:6',
                'student_name' => 'required',
                'class_id' => 'required',
            ]);

            $path = $siswa->photo;

            if ($request->hasFile('photo')) {
                if ($siswa->photo && Storage::disk('public')->exists($siswa->photo)) {
                    Storage::disk('public')->delete($siswa->photo);
                }
                $path = $request->file('photo')->store('siswa', 'public');
            }

            $user->update([
                'name' => $request->student_name,
                'email' => $request->email,
                'password' => $request->filled('password')
                    ? Hash::make($request->password)
                    : $user->password,
            ]);

            $siswa->update([
                'nisn' => $request->nisn,
                'student_name' => $request->student_name,
                'photo' => $path,
                'class_id' => $request->class_id,
            ]);
        },
        'Data siswa berhasil diperbarui!',
        'Gagal mengedit siswa.',
        'admin.siswa.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        return $this->tryCatchResponse(function () use ($siswa) {
            $user = $siswa->user;

            if ($siswa->photo && Storage::disk('public')->exists($siswa->photo)) {
                Storage::disk('public')->delete($siswa->photo);
            }

            $siswa->delete();
            if ($user) {
                $user->delete();
            }
        },
        'Data siswa berhasil dihapus!',
        'Gagal menghapus siswa.',
        'admin.siswa.index'
        );
    }
}
