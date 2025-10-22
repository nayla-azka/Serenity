<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\KonselorDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Counselor as Konselor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KonselorController extends AdminBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(KonselorDataTable $konselorDataTable)
    {
       return $konselorDataTable->render('admin.konselor.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.konselor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->tryCatchResponse(function () use ($request) {
            $request->validate([
                'nip' => 'required|unique:counselor,nip',
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'counselor_name' => 'required',
                'contact' => 'required',
                'desc' => 'required|string',
                'kelas' => 'required',
            ]);

            $user = User::create([
                'name' => $request->counselor_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'konselor',
            ]);

            $path = $request->hasFile('photo')
                ? $request->file('photo')->store('konselor', 'public')
                : 'default.jpg';

            Konselor::create([
                'nip' => $request->nip,
                'counselor_name' => $request->counselor_name,
                'photo' => $path,
                'kelas' => $request->kelas,
                'contact' => $request->contact,
                'desc' => $request->desc,
                'user_id' => $user->id,
            ]);
        },
        'Data konselor berhasil ditambah!',
        'Gagal menambah konselor.',
        'admin.konselor.index'
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
    public function edit($id)
    {
        $konselor = Konselor::with('user')->findOrFail($id); 
        return view('admin.konselor.edit', compact('konselor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Konselor $konselor)
    {
        return $this->tryCatchResponse(function () use ($request, $konselor) {
            $user = $konselor->user;

            $request->validate([
                'nip' => 'required|unique:counselor,nip,' . $konselor->id_counselor . ',id_counselor',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:6',
                'counselor_name' => 'required',
                'contact' => 'required',
                'desc' => 'required|string',
                'kelas' => 'required',
            ]);

            $path = $konselor->photo;

            if ($request->hasFile('photo')) {
                if ($konselor->photo && Storage::disk('public')->exists($konselor->photo)) {
                    Storage::disk('public')->delete($konselor->photo);
                }
                $path = $request->file('photo')->store('konselor', 'public');
            }

            $user->update([
                'name' => $request->counselor_name,
                'email' => $request->email,
                'password' => $request->filled('password')
                    ? Hash::make($request->password)
                    : $user->password,
            ]);

            $konselor->update([
                'nip' => $request->nip,
                'counselor_name' => $request->counselor_name,
                'photo' => $path,
                'kelas' => $request->kelas,
                'contact' => $request->contact,
                'desc' => $request->desc,
            ]);
        },
        'Data konselor berhasil diperbarui!',
        'Gagal mengedit konselor.',
        'admin.konselor.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Konselor $konselor)
    {
        return $this->tryCatchResponse(function () use ($konselor) {
            $user = $konselor->user;

            if ($konselor->photo && Storage::disk('public')->exists($konselor->photo)) {
                Storage::disk('public')->delete($konselor->photo);
            }

            $konselor->delete();
            if ($user) {
                $user->delete();
            }
        },
        'Data konselor berhasil dihapus!',
        'Gagal menghapus konselor.',
        'admin.konselor.index'
        );
    }
}
