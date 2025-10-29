<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ArtikelController extends Controller
{
public function index(Request $request)
{
    $query = $request->input('q');

    if ($query) {
        // Mode pencarian
        $artikels = Artikel::query()
            ->where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->orderByDesc('created_at') 
            ->paginate(10);

        return view('public.artikel', compact('artikels', 'query'));
    }

    // Mode default (tanpa pencarian)
    $featured  = Artikel::latest()->first();

    $latest = Artikel::when($featured, fn($q) => 
                    $q->where('article_id', '!=', $featured->article_id))
                    ->latest()
                    ->take(6)
                    ->get();

    $artikels = Artikel::latest()->take(20)->get(); 

    return view('public.artikel', compact('featured', 'latest', 'artikels'))
            ->with('query', null);
}

    public function show($article_id)
    {
        // Ambil data untuk tampilan (pakai view article_overview)
        $overview = DB::table('article_overview')
            ->where('article_id', $article_id)
            ->first();

        // Ambil Eloquent model untuk commentify
        $artikel = Artikel::findOrFail($article_id);

       // Add data from overview to model
        $artikel->total_views = $overview->total_views ?? 0;
        $artikel->total_comments = $overview->total_comments ?? 0;
        $artikel->title = $overview->title ?? $artikel->title;
        $artikel->content = $overview->content ?? $artikel->content;
        $artikel->author_name = $overview->author_name ?? 'Unknown';

        // Handle likes
        $likesCount = DB::table('likes')
            ->where('target_type', 'article')
            ->where('target_id', $article_id)
            ->count();
        
        $artikel->total_likes = $likesCount;
        $artikel->liked_by_user = false;

        if (Auth::check()) {
            $artikel->liked_by_user = DB::table('likes')
                ->where('user_id', Auth::id())
                ->where('target_type', 'article')
                ->where('target_id', $article_id)
                ->exists();
        }

         // 🔹 Unique + engagement views
        DB::table('article_views')->updateOrInsert(
            [
                'article_id' => $article_id,
                'user_id'    => Auth::id(),
                'ip_address' => request()->ip(),
            ],
            [
                'created_at'     => now(), // set only if new
                'last_viewed_at' => now(), // always updated
            ]
        );

        // Artikel lainnya
        $artikelLainnya = DB::table('article_overview')
            ->where('article_id', '<>', $article_id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('public.artikel_show', [
            'artikel'           => $artikel,       
            'artikelLainnya'    => $artikelLainnya,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $results = Artikel::where('title', 'like', "%{$query}%")
            ->take(5) // maksimal 5 hasil
            ->get(['article_id', 'title']); 

        return response()->json($results);
    }
}