<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artikel;
use App\Models\Counselor;
use App\Models\Banner;


class homeController extends Controller
{
    public function index(){
        $banners = Banner::latest()->get();
        $artikels = Artikel::latest()->take(20)->get();
        $guru = Counselor::all();
        return view('public.index', compact(
            'banners', 'artikels', 'guru'
        ));
    }
}