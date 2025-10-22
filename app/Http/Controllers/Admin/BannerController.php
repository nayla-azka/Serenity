<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Banner;
use App\DataTables\BannerDataTable;

class BannerController extends AdminBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(BannerDataTable $dataTable)
    {
        return $dataTable->render('admin.banner.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banner.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->tryCatchResponse(
            function () use ($request) {
                $request->validate([
                    'title' => 'required|string|max:255',
                    'desc'  => 'nullable|string',
                    'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                $path = $request->hasFile('photo')
                    ? $request->file('photo')->store('banners', 'public')
                    : null;

                Banner::create([
                    'title' => $request->title,
                    'desc'  => $request->desc,
                    'photo' => $path,
                ]);
            },
            'Banner berhasil ditambahkan.',
            'Gagal menambahkan banner.',
            'admin.banner.index'
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
    public function edit(Banner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
       return $this->tryCatchResponse(
            function () use ($request, $banner) {
                $request->validate([
                    'title' => 'required|string|max:255',
                    'desc'  => 'nullable|string',
                    'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                $path = $banner->photo;

                if ($request->hasFile('photo')) {
                    if ($banner->photo && Storage::disk('public')->exists($banner->photo)) {
                        Storage::disk('public')->delete($banner->photo);
                    }
                    $path = $request->file('photo')->store('banners', 'public');
                }

                $banner->update([
                    'title' => $request->title,
                    'desc'  => $request->desc,
                    'photo' => $path,
                ]);
            },
            'Banner berhasil diperbarui.',
            'Gagal memperbarui banner.',
            'admin.banner.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        return $this->tryCatchResponse(
            function () use ($banner) {
                if ($banner->photo && Storage::disk('public')->exists($banner->photo)) {
                    Storage::disk('public')->delete($banner->photo);
                }

                $banner->delete();
            },
            'Banner berhasil dihapus!',
            'Gagal menghapus banner.',
            'admin.banner.index',
            false // kalau gagal tetap redirect ke index
        );
    }
}
