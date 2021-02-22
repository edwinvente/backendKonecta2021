<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Post;
use App\Comment;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->get();
        return response()->json($posts, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = new Post();
        
        $post->category_id = (int)$request->input('category');
        $post->title = $request->input('title');
        $post->slug = Str::slug($post->title);
        $post->short_text = $request->input('shortText');
        $post->description = $request->input('description');

        $image = $request->file('file');
        if ($image) {
            //asignar nombre unico
            $image_name = time().$image->getClientOriginalName();
            //guardar en el sotorage carpeta users
            Storage::disk('posts')->put($image_name, File::get($image));
            //seteo el valor del usuario activo campo imagen
            $post->image = $image_name;
        }

        if ($post->save()) {
            return response()->json([
                'post' => $post,
                'status' => 'success'
            ], 200);
        }
        
        return response()->json([
            'post' => null,
            'status' => 'error'
        ], 200, $headers);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($post)
    {
        $post = Post::where('slug', $post)->first();
        $comments = Comment::where('post_id', $post->id)->get();
        return response()->json([
            'post' => $post,
            'comments' => $comments
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getImage($filename){
        $file = Storage::disk('posts')->get($filename);
        return new Response($file, 200);
    }
    
    public function comment(Request $request){
        $slug = $request->input('slug', 'indefinido');
        $post = Post::where('slug', $slug)->first();
        $comments = Comment::where('post_id', $post->id)->get();
        $user = JWTAuth::user();


        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->post_id = $post->id;
        $comment->comentario = $request->input('comment');

        if ($comment->save()) {
            return response()->json([
                'user' => $user,
                'comment' => $comment,
                'status' => 1
            ], 200);
        }

        return response()->json([
            'user' => $user,
            'comment' => 'Error al intentar guardar el comentario',
            'status' => 0
        ], 400);
    }

    public function comments($slug){
        $post = Post::where('slug', $slug)->first();
        $comments = Comment::where('post_id', $id)->get();
        return response()->json($comments, 200);
    }
    

}
