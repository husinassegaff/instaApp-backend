<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PostService;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     * Display the feed/timeline
     */
    public function index(): View
    {
        // Get all posts with pagination (15 per page)
        $posts = $this->postService->getAllPosts(15);

        return view('feed.index', compact('posts'));
    }
}
