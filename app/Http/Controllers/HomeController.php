<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    //
    public function index()
    {
        $posts = Post::latest()->paginate(6);
        return view('home')->with([
            'posts' => $posts
        ]);
    }
}
