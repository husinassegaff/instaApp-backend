<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * PostController
 *
 * Handles post CRUD operations following SOLID principles:
 * - SRP: Only handles HTTP post logic
 * - DIP: Depends on PostService (injected via constructor)
 * - OCP: Extensible without modifying existing code
 */
class PostController extends Controller
{
    /**
     * @param PostService $postService
     */
    public function __construct(
        private PostService $postService
    ) {}

    /**
     * Display a listing of posts
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Use PostService for business logic
        $posts = $this->postService->getAllPosts();

        // TODO Phase 10: Use PostResource::collection()
        return response()->json([
            'posts' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ], 200);
    }

    /**
     * Store a newly created post
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // TODO Phase 7: Replace with StorePostRequest
        $validated = $request->validate([
            'caption' => 'nullable|string|max:2200',
            'image' => 'required|string', // Base64 image
        ]);

        try {
            // Use PostService for business logic (includes validation and logging)
            $post = $this->postService->createPost($request->user(), $validated);

            // TODO Phase 10: Use PostResource
            return response()->json([
                'message' => 'Post created successfully.',
                'post' => $post,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified post
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        // Eager load relationships
        $post->load(['user', 'comments.user'])
            ->loadCount(['likes', 'comments']);

        // TODO Phase 10: Use PostResource
        return response()->json([
            'post' => $post,
        ], 200);
    }

    /**
     * Update the specified post
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        // TODO Phase 9: Replace with Policy authorization
        // Check if user owns the post
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized. You can only update your own posts.',
            ], 403);
        }

        // TODO Phase 7: Replace with UpdatePostRequest
        $validated = $request->validate([
            'caption' => 'nullable|string|max:2200',
            'image' => 'nullable|string', // Base64 image
        ]);

        try {
            // Use PostService for business logic (includes validation and logging)
            $post = $this->postService->updatePost($post, $validated);

            // TODO Phase 10: Use PostResource
            return response()->json([
                'message' => 'Post updated successfully.',
                'post' => $post,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified post
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        // TODO Phase 9: Replace with Policy authorization
        // Check if user owns the post
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized. You can only delete your own posts.',
            ], 403);
        }

        // Use PostService for business logic (includes logging)
        $this->postService->deletePost($post);

        return response()->json([
            'message' => 'Post deleted successfully.',
        ], 200);
    }
}
