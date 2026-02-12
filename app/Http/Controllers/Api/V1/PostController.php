<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->posts()->with('author');

           // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // Apply sorting
        $allowedSortFields = ['created_at', 'title', 'body', 'updated_at'];
        $sortField = 'created_at';
        $sortDirection = 'desc';

        if ($request->has('sort') && !empty($request->sort)) {
            $sort = $request->sort;
            if (str_starts_with($sort, '-')) {
                $sortField = substr($sort, 1);
                $sortDirection = 'desc';
            } else {
                $sortField = $sort;
                $sortDirection = 'asc';
            }
        }

        // Validate sort field
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
            $sortDirection = 'desc';
        }

        $query->orderBy($sortField, $sortDirection);
        $posts = $query->paginate($request->get('per_page', 15));
        return response()->json([
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        $data['author_id'] = $request->user()->id;
        $post = Post::create($data);
        return response()->json([
            'message' => 'New post created successfully.',
            'data' => new PostResource($post)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        abort_if(Auth::id() != $post->author_id, 403, "Access forbiden");
        return response()->json(new PostResource($post), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        abort_if(Auth::id() != $post->author_id, 403, "Access forbiden");

        $data = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);


        $post->update($data);
        return response()->json([
            'message' => 'post updated successfully.',
            'data' => new PostResource($post)
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {

        abort_if(Auth::id() != $post->author_id, 403, "Access forbiden");
        $post->delete();
        return response()->noContent();
    }
}
