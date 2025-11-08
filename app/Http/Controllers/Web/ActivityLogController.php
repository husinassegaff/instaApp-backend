<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display the authenticated user's activity logs
     */
    public function index(): View
    {
        $activities = auth()->user()
            ->activityLogs()
            ->latest()
            ->paginate(20);

        return view('activity.index', compact('activities'));
    }
}
