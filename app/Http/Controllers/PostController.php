<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
     
        $perPage = 100;
        $page =  $request->input('page', 1);
    
        $posts = Cache::remember("posts:page:$page", 60, function () use ($perPage) {
            return DB::table('posts')->paginate($perPage, ["title", "desc"]);
        });
    
        if ($posts->count() > 0) {
            return response()->json([
                'data' => $posts->items(),
                'current_page' => $posts->currentPage(),
              
                'total' => $posts->total(),
                'message' => 'Posts retrieved successfully',
                'status' => 200
            ]);
        } else if ($posts->count() === 0){
            return response()->json(['message' => 'page tidak ditemukan'], 404);
        } else {
            return response()->json(['message' => 'Posts not found'], 404);
        }
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
                return response()->json(['message' => 'Post berhasil di buat'], 201);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
