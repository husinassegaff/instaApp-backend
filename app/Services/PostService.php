<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        return Post::with(['user'])
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
     * @throws ValidationException
     */
    public function createPost(User $user, array $data): Post
    {
        // Validate base64 image format
        if (! $this->isValidBase64Image($data['image'])) {
            throw ValidationException::withMessages([
                'image' => ['Invalid base64 image format. Please provide a valid base64 encoded image.'],
            ]);
        }

        // Create post
        $post = Post::create([
            'user_id' => $user->id,
            'caption' => $data['caption'] ?? null,
            'image' => $data['image'],
        ]);

        // Load user relationship
        $post->load('user');

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'post',
            description: 'Created a new post',
            subject: $post
        );

        return $post;
    }

    /**
     * Update a post
     *
     * @param Post $post
     * @param array $data Validated post data
     * @return Post
     * @throws ValidationException
     */
    public function updatePost(Post $post, array $data): Post
    {
        // Validate base64 image if provided
        if (isset($data['image']) && ! $this->isValidBase64Image($data['image'])) {
            throw ValidationException::withMessages([
                'image' => ['Invalid base64 image format. Please provide a valid base64 encoded image.'],
            ]);
        }

        // Update post
        $post->update($data);

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

        return $post;
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

        return $deleted;
    }

    /**
     * Validate base64 image format
     *
     * @param string $base64String
     * @return bool
     */
    private function isValidBase64Image(string $base64String): bool
    {
        // Check if string contains valid base64 image data
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            // Valid image types
            $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
            return in_array(strtolower($matches[1]), $allowedTypes);
        }

        return false;
    }
}
