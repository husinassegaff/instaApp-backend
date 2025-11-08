<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     * Show the form for creating a new post
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        Log::info('PostController: Store request received', [
            'user_id' => auth()->id(),
            'has_image' => !empty($request->input('image')),
            'has_caption' => !empty($request->input('caption')),
        ]);

        try {
            $post = $this->postService->createPost(auth()->user(), $request->validated());

            Log::info('PostController: Post created successfully', [
                'post_id' => $post->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('feed')->with('success', 'Post created successfully!');
        } catch (\Exception $e) {
            Log::error('PostController: Post creation failed', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors([
                'error' => 'Failed to create post. Please try again.',
            ])->withInput();
        }
    }

    /**
     * Display the specified post
     */
    public function show(Post $post): View
    {
        // Load relationships
        $post->load(['user', 'likes', 'comments.user']);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post): View
    {
        // Authorization
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        Log::info('PostController: Update request received', [
            'post_id' => $post->id,
            'user_id' => auth()->id(),
        ]);

        try {
            // Authorization
            $this->authorize('update', $post);

            $updatedPost = $this->postService->updatePost($post, $request->validated());

            Log::info('PostController: Post updated successfully', [
                'post_id' => $updatedPost->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('posts.show', $post->id)->with('success', 'Post updated successfully!');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('PostController: Unauthorized update attempt', [
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'post_owner_id' => $post->user_id,
            ]);

            return back()->withErrors(['error' => 'You are not authorized to update this post.']);
        } catch (\Exception $e) {
            Log::error('PostController: Post update failed', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'post_id' => $post->id,
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors([
                'error' => 'Failed to update post. Please try again.',
            ])->withInput();
        }
    }

    /**
     * Remove the specified post
     */
    public function destroy(Post $post): RedirectResponse
    {
        Log::info('PostController: Delete request received', [
            'post_id' => $post->id,
            'user_id' => auth()->id(),
        ]);

        try {
            // Authorization
            $this->authorize('delete', $post);

            $this->postService->deletePost($post);

            Log::info('PostController: Post deleted successfully', [
                'post_id' => $post->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('feed')->with('success', 'Post deleted successfully!');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('PostController: Unauthorized delete attempt', [
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'post_owner_id' => $post->user_id,
            ]);

            return back()->withErrors(['error' => 'You are not authorized to delete this post.']);
        } catch (\Exception $e) {
            Log::error('PostController: Post deletion failed', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'post_id' => $post->id,
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors([
                'error' => 'Failed to delete post. Please try again.',
            ]);
        }
    }
}
