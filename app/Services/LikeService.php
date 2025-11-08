<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

/**
 * LikeService
 *
 * SOLID Principles:
 * - SRP: Single responsibility - handles ONLY like/unlike business logic
 * - DIP: Depends on ActivityLoggerService abstraction (injected)
 */
class LikeService
{
    /**
     * @param ActivityLoggerService $activityLogger
     */
    public function __construct(
        private ActivityLoggerService $activityLogger
    ) {}

    /**
     * Like a post
     *
     * @param User $user
     * @param Post $post
     * @return Like
     * @throws ValidationException
     */
    public function likePost(User $user, Post $post): Like
    {
        try {
            // Create like record
            $like = Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);

            // Log activity
            $this->activityLogger->log(
                user: $user,
                logName: 'like',
                description: 'Liked a post',
                subject: $post
            );

            return $like;

        } catch (QueryException $e) {
            // Handle duplicate like (unique constraint violation)
            if ($e->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'like' => ['You have already liked this post.'],
                ]);
            }

            throw $e;
        }
    }

    /**
     * Unlike a post
     *
     * @param User $user
     * @param Post $post
     * @return bool
     * @throws ValidationException
     */
    public function unlikePost(User $user, Post $post): bool
    {
        // Find and delete like record
        $deleted = Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->delete();

        if (! $deleted) {
            throw ValidationException::withMessages([
                'like' => ['You have not liked this post.'],
            ]);
        }

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'like',
            description: 'Unliked a post',
            subject: $post
        );

        return true;
    }

    /**
     * Check if user has liked a post
     *
     * @param Post $post
     * @param User $user
     * @return bool
     */
    public function isLikedByUser(Post $post, User $user): bool
    {
        return Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->exists();
    }
}
