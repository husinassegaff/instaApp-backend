<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * PostService
 *
 * SOLID Principles:
 * - SRP: Single responsibility - handles ONLY post business logic
 * - DIP: Depends on ActivityLoggerService abstraction (injected)
 * - OCP: Open for extension (can add image processing, filters, etc.)
 */
class PostService
{
    /**
     * @param ActivityLoggerService $activityLogger
     */
    public function __construct(
        private ActivityLoggerService $activityLogger
    ) {}

    /**
     * Get all posts with pagination
     *
     * @param int $perPage Number of posts per page
     * @return LengthAwarePaginator
     */
    public function getAllPosts(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['user', 'likes'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new post
     *
     * @param User $user
     * @param array $data Validated post data
     * @return Post
     * @throws \Exception
     */
    public function createPost(User $user, array $data): Post
    {
        Log::info('PostService: Starting post creation', [
            'user_id' => $user->id,
            'has_caption' => !empty($data['caption']),
            'image_length' => strlen($data['image']),
        ]);

        return DB::transaction(function () use ($user, $data) {
            // Create post
            $post = Post::create([
                'user_id' => $user->id,
                'caption' => $data['caption'] ?? null,
                'image' => $data['image'],
            ]);

            if (!$post) {
                Log::error('PostService: Failed to create post in database');
                throw new \RuntimeException('Failed to create post in database');
            }

            Log::info('PostService: Post created successfully', ['post_id' => $post->id]);

            // Load user relationship
            $post->load('user');

            // Log activity
            $this->activityLogger->log(
                user: $user,
                logName: 'post',
                description: 'Created a new post',
                subject: $post
            );

            Log::info('PostService: Activity logged successfully', ['post_id' => $post->id]);

            return $post;
        });
    }

    /**
     * Update a post
     *
     * @param Post $post
     * @param array $data Validated post data
     * @return Post
     * @throws \Exception
     */
    public function updatePost(Post $post, array $data): Post
    {
        Log::info('PostService: Starting post update', [
            'post_id' => $post->id,
            'updated_fields' => array_keys($data),
        ]);

        return DB::transaction(function () use ($post, $data) {
            // Update post
            $post->update($data);

            Log::info('PostService: Post updated successfully', ['post_id' => $post->id]);

            // Reload user relationship
            $post->load('user');

            // Log activity
            $this->activityLogger->log(
                user: $post->user,
                logName: 'post',
                description: 'Updated post',
                subject: $post,
                properties: ['updated_fields' => array_keys($data)]
            );

            Log::info('PostService: Activity logged for update', ['post_id' => $post->id]);

            return $post;
        });
    }

    /**
     * Delete a post
     *
     * @param Post $post
     * @return bool
     */
    public function deletePost(Post $post): bool
    {
        $user = $post->user;
        $postId = $post->id;

        Log::info('PostService: Deleting post', ['post_id' => $postId]);

        // Delete the post
        $deleted = $post->delete();

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'post',
            description: 'Deleted post',
            subject: null,
            properties: ['post_id' => $postId]
        );

        Log::info('PostService: Post deleted successfully', ['post_id' => $postId]);

        return $deleted;
    }
}
