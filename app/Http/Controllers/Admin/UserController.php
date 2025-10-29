<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends AdminBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserDataTable $userDataTable)
    {
        return $userDataTable->render('admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->tryCatchResponse(function () use ($request) {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role'     => 'required|string',
            ]);

            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);
        },
        'User berhasil ditambahkan!',
        'Gagal menambahkan user.',
        'admin.user.index'
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
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $user = User::findOrFail($id);

        return $this->tryCatchResponse(function () use ($request, $user) {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:6',
                'role'     => 'required|string',
            ]);

            $user->name  = $request->name;
            $user->email = $request->email;
            $user->role  = $request->role;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();
        },
        'User berhasil diperbarui!',
        'Gagal memperbarui user.',
        'admin.user.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        return $this->tryCatchResponse(function () use ($user) {
            $user->delete();
        },
        'User berhasil dihapus!',
        'Gagal menghapus user.',
        'admin.user.index'
        );
    }
}
