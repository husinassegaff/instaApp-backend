<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * LikeController
 *
 * Handles like/unlike operations following SOLID principles:
 * - SRP: Only handles HTTP like operations
 * - DIP: Depends on LikeService (injected via constructor)
 */
class LikeController extends Controller
{
    /**
     * @param LikeService $likeService
     */
    public function __construct(
        private LikeService $likeService
    ) {}

    /**
     * Like a post
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function like(Request $request, Post $post): JsonResponse
    {
        try {
            // Use LikeService for business logic (includes duplicate checking and logging)
            $like = $this->likeService->likePost($request->user(), $post);

            return response()->json([
                'message' => 'Post liked successfully.',
                'like' => $like,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Unlike a post
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function unlike(Request $request, Post $post): JsonResponse
    {
        try {
            // Use LikeService for business logic (includes logging)
            $this->likeService->unlikePost($request->user(), $post);

            return response()->json([
                'message' => 'Post unliked successfully.',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
