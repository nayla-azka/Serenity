<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Artikel;
use App\DataTables\ArtikelDataTable;
use Illuminate\Support\Facades\Auth;

class ArtikelController extends AdminBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(ArtikelDataTable $dataTable)
    {
        return $dataTable->render('admin.artikel.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.artikel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         return $this->tryCatchResponse(
            function () use ($request) {
                $request->validate([
                    'title'   => 'required|string|max:255',
                    'content' => 'nullable|string',
                    'photo'   => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                $path = $request->file('photo')
                    ? $request->file('photo')->store('artikel_thumbs', 'public')
                    : null;

                Artikel::create([
                    'title'     => $request->title,
                    'content'   => $request->input('content'),
                    'photo'     => $path,
                    'author_id' => Auth::id(),
                ]);
            },
            'Artikel berhasil ditambahkan.',
            'Gagal menambahkan artikel.',
            'admin.artikel.index'
        );
    }

    public function upload(Request $request)
    {
        return $this->tryCatchJsonResponse(function () use ($request) {
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $path = $file->store('artikel', 'public');

                return response()->json([
                    'uploaded' => true,
                    'url'      => asset('storage/' . $path),
                ]);
            }

            return response()->json([
                'uploaded' => false,
                'error'    => [
                    'message' => 'No file uploaded.'
                ]
            ], 400);
        }, 'Upload gagal');
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
        $artikel = Artikel::findOrFail($id);
        return view('admin.artikel.edit', compact('artikel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Artikel $artikel)
    {
        return $this->tryCatchResponse(
            function () use ($request, $artikel) {
                // Validasi
                $request->validate([
                    'title'   => 'required|string|max:255',
                    'content' => 'nullable|string',
                    'photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                // Hapus gambar konten yang sudah tidak dipakai
                $oldImages = $this->extractImages($artikel->content);
                $newImages = $this->extractImages($request->input('content'));
                $deletedImages = array_diff($oldImages, $newImages);

                foreach ($deletedImages as $img) {
                    if (str_contains($img, '/storage/')) {
                        $path = str_replace(asset('storage') . '/', '', $img);
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                }

                // Update photo thumbnail
                $path = $artikel->photo;
                if ($request->hasFile('photo')) {
                    if ($artikel->photo && Storage::disk('public')->exists($artikel->photo)) {
                        Storage::disk('public')->delete($artikel->photo);
                    }
                    $path = $request->file('photo')->store('artikel_thumbs', 'public');
                }

                $artikel->update([
                    'title'   => $request->title,
                    'content' => $request->input('content'),
                    'photo'   => $path,
                ]);
            },
            'Artikel berhasil diperbarui.',
            'Gagal memperbarui artikel.',
            'admin.artikel.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artikel $artikel)
    {
        return $this->tryCatchResponse(
            function () use ($artikel) {
                // Hapus semua gambar dari content
                $images = $this->extractImages($artikel->content);
                foreach ($images as $img) {
                    if (str_contains($img, '/storage/')) {
                        $path = str_replace(asset('storage') . '/', '', $img);
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                }

                // Hapus thumbnail
                if ($artikel->photo && Storage::disk('public')->exists($artikel->photo)) {
                    Storage::disk('public')->delete($artikel->photo);
                }

                $artikel->delete();
            },
            'Artikel berhasil dihapus.',
            'Gagal menghapus artikel.',
            'admin.artikel.index',
            false // kalau gagal hapus, tetap balik ke index, bukan back
        );
    }

    private function extractImages(?string $content): array
    {
        if (!$content) return [];
        preg_match_all('/<img[^>]+src="([^">]+)"/i', $content, $matches);
        return $matches[1] ?? [];
    }

}
