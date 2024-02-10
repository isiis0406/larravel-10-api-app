<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {

        try {
            $query = Post::query();
            $perPage = $request->limit ?? 10;
            $page = $request->page ?? 1;
            $search = $request->search ?? null;
            if ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            }

            $total = $query->count();
            $result = $query->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            $posts = Post::latest()->get();

            if ($posts->isEmpty()) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'No posts found'
                ], 404);
            } else {
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Posts fetched successfully',
                    'current_page' => (int)$page,
                    'last_page' => ceil($total / $perPage),
                    'posts' => $result
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Error while fetching posts',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json($posts, 200);
    }

    public function store(CreatePostRequest $request)
    {
        try {
            $post = new Post();

            $post->title = $request->title;
            $post->description = $request->description;
            $post->save();

            return response()->json([
                'statusCode' => 201,
                'message' => 'Post created successfully',
                'data' => $post
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Error while creating post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        try {

            if (!$post) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Post not found'
                ], 404);
            }

            $post->title = $request->title;
            $post->description = $request->description;
            $post->save();

            return response()->json([
                'statusCode' => 200,
                'message' => 'Post updated successfully',
                'data' => $post
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Error while updating post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {

        try {
            $post = Post::find($id);
            if (!$post) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Post not found'
                ], 404);
            }

            $post->delete();

            return response()->json([
                'statusCode' => 200,
                'message' => 'Post deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Error while deleting post',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
