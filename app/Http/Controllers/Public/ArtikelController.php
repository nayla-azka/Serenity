<?php

namespace App\Http\Controllers\Public;

use App\Models\Artikel;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ArtikelController extends PublicBaseController
{
    public function index()
    {
        $featured  = Artikel::latest()->first();

        // Take next 6 newest excluding the featured (if any)
        $latest = Artikel::when($featured, fn($q) => $q->where('article_id', '!=', $featured->article_id))
                         ->latest()
                         ->take(6)
                         ->get();

        // For “Forum Relevan” (horizontal cards)
        $artikels = Artikel::latest()->take(20)->get();

        // Make sure the view name matches the file path below
        return view('public.artikel', compact('featured', 'latest', 'artikels'));
    }

    // public function show($article_id)
    // {
    //     $artikel = DB::table('article_overview')->where('article_id', $article_id)->first();
    //     $artikel = Artikel::with(['comments.user'])->findOrFail($article_id);
    //     $artikel = Artikel::withCount('likes')->findOrFail($article_id);  
    //     $comments = Comment::withCount('likes')->get();  


    //     $artikel = DB::table('article_overview')
    //     ->where('article_id', $article_id)
    //     ->first();

    //     // Simpan view baru
    //     DB::table('article_views')->updateOrInsert(
    //         [
    //             'article_id' => $article_id,
    //             'user_id'    => Auth::id(),
    //             'ip_address' => request()->ip(),
    //         ],
    //         [
    //             'created_at' => now(),
    //         ]
    //     );

    //     $comments = Comment::with(['user', 'replies.user'])
    //         ->where('article_id', $article_id)
    //         ->where('is_removed', 0)
    //         ->latest()
    //         ->get();

    //     $artikelLainnya = DB::table('article_overview')
    //         ->where('article_id', '<>', $article_id)
    //         ->orderBy('created_at', 'desc')
    //         ->limit(3)
    //         ->get();

    //     return view('public.artikel_show', compact('artikel', 'artikelLainnya', 'comments'));
    // }

    public function show($article_id)
    {
        // Ambil data artikel dari view (supaya ada total_views & total_comments)
        $artikel = DB::table('article_overview')
            ->where('article_id', $article_id)
            ->first();

        if (!$artikel) {
            abort(404);
        }

        // Tambahkan hitungan likes ke artikel
        $likesCount = DB::table('likes')
            ->where('target_type', 'article')
            ->where('target_id', $article_id)
            ->count();

        // Simpan view baru
        DB::table('article_views')->updateOrInsert(
            [
                'article_id' => $article_id,
                'user_id'    => Auth::id(),
                'ip_address' => request()->ip(),
            ],
            [
                'created_at' => now(),
            ]
        );

        // Ambil komentar untuk artikel ini, plus likes dan replies
        $comments = Comment::withCount('likes')
            ->with([
                'user',
                'replies' => fn($q) => $q->withCount('likes')->with('user')
            ])
            ->where('article_id', $article_id)
            ->where('is_removed', 0)
            ->latest()
            ->get();

        // Artikel lainnya
        $artikelLainnya = DB::table('article_overview')
            ->where('article_id', '<>', $article_id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('public.artikel_show', [
            'artikel'        => $artikel,
            'likesCount'     => $likesCount,
            'artikelLainnya' => $artikelLainnya,
            'comments'       => $comments,
        ]);
    }
}