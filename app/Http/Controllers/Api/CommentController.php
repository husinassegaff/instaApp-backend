<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
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

        // TODO Use CommentResource::collection()
        return response()->json([
            'comments' => $comments,
        ], 200);
    }

    /**
     * Store a new comment
     *
     * @param StoreCommentRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        // Validation handled by StoreCommentRequest (Single Responsibility Principle)
        $validated = $request->validated();

        // Use CommentService for business logic (includes logging)
        $comment = $this->commentService->createComment(
            $request->user(),
            $post,
            $validated['content']
        );

        // TODO Use CommentResource
        return response()->json([
            'message' => 'Comment added successfully.',
            'comment' => $comment,
        ], 201);
    }

    /**
     * Update a comment
     *
     * @param UpdateCommentRequest $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        // Authorization handled by CommentPolicy (Single Responsibility Principle)
        $this->authorize('update', $comment);

        // Validation handled by UpdateCommentRequest
        $validated = $request->validated();

        // Use CommentService for business logic (includes logging)
        $comment = $this->commentService->updateComment($comment, $validated['content']);

        // TODO Use CommentResource
        return response()->json([
            'message' => 'Comment updated successfully.',
            'comment' => $comment,
        ], 200);
    }

    /**
     * Delete a comment
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        // Authorization handled by CommentPolicy (Single Responsibility Principle)
        $this->authorize('delete', $comment);

        // Use CommentService for business logic (includes logging)
        $this->commentService->deleteComment($comment);

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ], 200);
    }
}
