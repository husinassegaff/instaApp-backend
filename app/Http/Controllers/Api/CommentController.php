<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CommentController
 *
 * Handles comment CRUD operations following SOLID principles:
 * - SRP: Only handles HTTP comment logic
 * - DIP: Depends on CommentService (injected via constructor)
 */
class CommentController extends Controller
{
    /**
     * @param CommentService $commentService
     */
    public function __construct(
        private CommentService $commentService
    ) {}

    /**
     * Display comments for a post
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function index(Post $post): JsonResponse
    {
        // Use CommentService for business logic
        $comments = $this->commentService->getComments($post);

        // TODO Phase 10: Use CommentResource::collection()
        return response()->json([
            'comments' => $comments,
        ], 200);
    }

    /**
     * Store a new comment
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        // TODO Phase 7: Replace with StoreCommentRequest
        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        // Use CommentService for business logic (includes logging)
        $comment = $this->commentService->createComment(
            $request->user(),
            $post,
            $validated['content']
        );

        // TODO Phase 10: Use CommentResource
        return response()->json([
            'message' => 'Comment added successfully.',
            'comment' => $comment,
        ], 201);
    }

    /**
     * Update a comment
     *
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        // TODO Phase 9: Replace with Policy authorization
        // Check if user owns the comment
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized. You can only update your own comments.',
            ], 403);
        }

        // TODO Phase 7: Replace with UpdateCommentRequest
        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        // Use CommentService for business logic (includes logging)
        $comment = $this->commentService->updateComment($comment, $validated['content']);

        // TODO Phase 10: Use CommentResource
        return response()->json([
            'message' => 'Comment updated successfully.',
            'comment' => $comment,
        ], 200);
    }

    /**
     * Delete a comment
     *
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        // TODO Phase 9: Replace with Policy authorization
        // Check if user owns the comment
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized. You can only delete your own comments.',
            ], 403);
        }

        // Use CommentService for business logic (includes logging)
        $this->commentService->deleteComment($comment);

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ], 200);
    }
}
