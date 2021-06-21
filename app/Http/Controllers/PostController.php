<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $posts = Post::latest()->paginate(6);
        return view('home')->with([
            'posts' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if ($request->has('image')) {
            $file = $request->image;
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $image_name);
        }
        Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'slug' => Str::slug($request->title),
            'image' => $image_name,
            'user_id' => auth()->user()->id
        ]);
        return redirect()->route('home')->with([
            'success' => 'article ajouté'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
        return view('show')->with([
            'post' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
        return view('edit')->with([
            'post' => $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
        if ($request->has('image')) {
            $file = $request->image;
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $image_name);
            unlink(public_path('uploads') . '/' . $post->image);
            $post->image = $image_name;
        }
        $post->update([
            'title' => $request->title,
            'body' => $request->body,
            'slug' => Str::slug($request->title),
            'image' => $post->image,
            'user_id' => auth()->user()->id
        ]);
        return redirect()->route('home')->with([
            'success' => 'Article modifié'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
        if (file_exists(public_path('./uploads/') . $post->image)) {
            unlink(public_path('uploads') . '/' . $post->image);
        }
        $post->delete();
        return redirect()->route('home')->with([
            'success' => 'Article supprimé'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function delete($slug)
    {
        //
        $post = Post::withTrashed()->where('slug', $slug)->first();
        if (file_exists(public_path('./uploads/') . $post->image)) {
            unlink(public_path('uploads') . '/' . $post->image);
        }
        $post->forceDelete();
        return redirect()->route('home')->with([
            'success' => 'Article supprimé définitivement'
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function restore($slug)
    {
        //
        $post = Post::withTrashed()->where('slug', $slug)->first();
        $post->restore();
        return redirect()->route('home')->with([
            'success' => 'Article récuperé'
        ]);
    }
}
