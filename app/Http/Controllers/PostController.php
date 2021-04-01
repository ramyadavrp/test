<?php
 
namespace App\Http\Controllers;
 
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
 
class PostController extends Controller
{
    public function index()
    {
        $posts = auth()->user()->posts;
 
        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }
 
    public function show($id)
    {
        $post = auth()->user()->posts()->find($id);
 
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post with id ' . $id . ' not found'
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $post->toArray()
        ], 400);
    }
 
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'type' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'time_of_post' => 'required',
            'ip_address' => 'required'
        ]);
 
        $post = new Post();
        $post->user_id = $request->user_id;
        $post->type = $request->type;
		$post->latitude = $request->latitude;
        $post->longitude = $request->longitude;
        $post->time_of_post = $request->time_of_post;
        $post->ip_address = $request->ip_address;
 
        if (auth()->user()->posts()->save($post))
            return response()->json([
                'success' => true,
				'message' => 'Post added Successfully',
                'data' => $post->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Post could not be added'
            ], 500);
    }
 
    public function update(Request $request, $id)
    {
        $post = auth()->user()->posts()->find($id);
 
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post with id ' . $id . ' not found'
            ], 400);
        }
 
        $updated = $post->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Post could not be updated'
            ], 500);
    }
 
    public function destroy($id)
    {
        $post = auth()->user()->posts()->find($id);
 
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post with id ' . $id . ' not found'
            ], 400);
        }
 
        if ($post->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Post could not be deleted'
            ], 500);
        }
    }
}