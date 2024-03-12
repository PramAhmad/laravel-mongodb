<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    
    public function index(Request $request)
    {
     
        $perPage = 100;
        $page =  $request->input('page', 1);
    
        $posts = Cache::remember("posts:page:$page", 60, function () use ($perPage) {          
            return DB::table("posts")->paginate($perPage);
        });
        
        if ($posts->count() > 0) {
            return response()->json([
                'data' => $posts,
                'message' => 'Posts berhasil di ambil',
                'status' => 200
            ]);
        } else if ($posts->count() === 0){
            return response()->json(['message' => 'Page tidak ditemukan'], 404);
        } else {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }
    }
    
    public function create()
    {
        
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'title' => 'required|unique:posts|string|max:255',
                'desc' => 'required|string',
                'slug' => 'required|unique:posts|string|max:255'
            ]);
    
            $post = DB::table('posts')->insert([
                'title' => $request->title,
                'desc' => $request->desc,
                'slug' => $request->slug
            ]);
    
            if ($post) {
                return response()->json([
                    'data' => DB::table('posts')->where('title', $request->title)->first(),
                    'message' => 'Post berhasil di buat',
                    'status' => 201,
                ]);
            } 
    
            else {    
                return response()->json(['message' => 'Post tidak berhasil di buat'], 500);
            }
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 422);
        }
      
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $post = Cache::remember("post:$slug", 60, function () use ($slug) {
           return DB::table('posts')->where('slug', $slug)->first();
        });
    
        if ($post) {
            return response()->json([
                'data' => $post,
                "slug" => $slug,
                'message' => 'Post berhasil di ambil',
                'status' => 200
            ]);
        } else {
            return response()->json(['message' => 'Post tidak di temukan'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    
       try {
           
        
            $request->validate([
                'title' => 'required|string|max:255',
                'desc' => 'required|string',
                
            ]);

            $data  = array(
                'title' => $request->title,
                'desc' => $request->desc,
                'slug' => $request->slug
            );
            $post = DB::table('posts')->find($id);

            if (!$post) {
                return response()->json(['message' => 'Post tidak di temukan'], 404);
            }
            $post = DB::table('posts')->where('_id', $id)->update($data);


    
            if ($post) {
                return response()->json([
                    'data' => DB::table('posts')->find($id),
                    'message' => 'Post berhasil di update'], 200);
            } else {
                return response()->json(['message' => 'Post tidak berhasil di update'], 500);
            }
          } catch (ValidationException $e) {
        return response()->json(['message' => $e->validator->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 422);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = DB::table('posts')->find($id);
        if (!$post) {
            return response()->json(['message' => 'Post tidak di temukan'], 404);
        }
        $post = DB::table('posts')->where('_id', $id)->delete();
        if ($post) {
            return response()->json(['message' => 'Post berhasil di hapus'], 200);
        } else {
            return response()->json(['message' => 'Post tidak berhasil di hapus'], 500);
        }
    }
}
