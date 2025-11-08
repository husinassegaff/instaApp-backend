<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
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
     * @param StorePostRequest $request
     * @return JsonResponse
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        // Validation handled by StorePostRequest (Single Responsibility Principle)
        $validated = $request->validated();

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
     * @param UpdatePostRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        // Authorization handled by PostPolicy (Single Responsibility Principle)
        $this->authorize('update', $post);

        // Validation handled by UpdatePostRequest
        $validated = $request->validated();

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
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        // Authorization handled by PostPolicy (Single Responsibility Principle)
        $this->authorize('delete', $post);

        // Use PostService for business logic (includes logging)
        $this->postService->deletePost($post);

        return response()->json([
            'message' => 'Post deleted successfully.',
        ], 200);
    }
}
