<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends AdminBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $konselor = null;
        if ($user->role === 'konselor') {
            $konselor = Counselor::where('user_id', $user->id)->first();
        }

        return view('admin.profile', compact('user', 'konselor'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        return $this->tryCatchResponse(
            function () use ($request, $user) {
                $validated = [];

                if ($user->role === 'konselor') {
                    // ✅ Validation rules for konselor
                    $validated = $request->validate([
                        'nip'            => 'required|string|max:20',
                        'counselor_name' => 'required|string|max:100',
                        'kelas'          => 'nullable|in:X,XI,"XII & XIII"',
                        'contact'        => 'required|string|max:15',
                        'desc'           => 'nullable|string',
                        'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
                        'password'       => 'nullable|string|min:8',
                        'photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    ]);

                    $konselor = Counselor::where('user_id', $user->id)->firstOrFail();
                    $path = $konselor->photo;

                    if ($request->hasFile('photo')) {
                        if ($konselor->photo && Storage::disk('public')->exists($konselor->photo)) {
                            Storage::disk('public')->delete($konselor->photo);
                        }
                        $path = $request->file('photo')->store('konselor', 'public');
                    }

                    // update konselor fields
                    $konselor->update([
                        'nip'            => $validated['nip'],
                        'counselor_name' => $validated['counselor_name'],
                        'kelas'          => $validated['kelas'] ?? null,
                        'contact'        => $validated['contact'],
                        'desc'           => $validated['desc'] ?? '',
                        'photo'          => $path
                    ]);

                    // update user fields
                    $userData = [
                        'name'  => $validated['counselor_name'],
                        'email' => $validated['email'],
                    ];
                    if (!empty($validated['password'])) {
                        $userData['password'] = bcrypt($validated['password']);
                    }
                    $user->update($userData);

                } else {
                    $validated = $request->validate([
                        'name'     => 'required|string|max:255',
                        'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
                        'password' => 'nullable|string|min:8',
                    ]);

                    $userData = [
                        'name'  => $validated['name'],
                        'email' => $validated['email'],
                    ];
                    if (!empty($validated['password'])) {
                        $userData['password'] = bcrypt($validated['password']);
                    }
                    $user->update($userData);
                }
            },
            'Profil berhasil diperbarui.',
            'Gagal memperbarui profil.',
            'admin.profile.index'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
