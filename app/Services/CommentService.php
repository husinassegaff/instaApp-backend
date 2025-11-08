<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * CommentService
 *
 * SOLID Principles:
 * - SRP: Single responsibility - handles ONLY comment business logic
 * - DIP: Depends on ActivityLoggerService abstraction (injected)
 */
class CommentService
{
    /**
     * @param ActivityLoggerService $activityLogger
     */
    public function __construct(
        private ActivityLoggerService $activityLogger
    ) {}

    /**
     * Get all comments for a post
     *
     * @param Post $post
     * @return Collection
     */
    public function getComments(Post $post): Collection
    {
        return $post->comments()
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Create a new comment
     *
     * @param User $user
     * @param Post $post
     * @param string $content
     * @return Comment
     */
    public function createComment(User $user, Post $post, string $content): Comment
    {
        // Create comment
        $comment = Comment::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'content' => $content,
        ]);

        // Load user relationship
        $comment->load('user');

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'comment',
            description: 'Commented on a post',
            subject: $comment,
            properties: ['post_id' => $post->id]
        );

        return $comment;
    }

    /**
     * Update a comment
     *
     * @param Comment $comment
     * @param string $content
     * @return Comment
     */
    public function updateComment(Comment $comment, string $content): Comment
    {
        // Update comment
        $comment->update([
            'content' => $content,
        ]);

        // Reload user relationship
        $comment->load('user');

        // Log activity
        $this->activityLogger->log(
            user: $comment->user,
            logName: 'comment',
            description: 'Updated comment',
            subject: $comment
        );

        return $comment;
    }

    /**
     * Delete a comment
     *
     * @param Comment $comment
     * @return bool
     */
    public function deleteComment(Comment $comment): bool
    {
        $user = $comment->user;
        $commentId = $comment->id;

        // Delete the comment
        $deleted = $comment->delete();

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'comment',
            description: 'Deleted comment',
            subject: null,
            properties: ['comment_id' => $commentId]
        );

        return $deleted;
    }
}
