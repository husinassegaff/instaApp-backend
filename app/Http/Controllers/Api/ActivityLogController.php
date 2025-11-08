<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * ActivityLogController
 *
 * Handles activity log retrieval following SOLID principles:
 * - SRP: Only handles HTTP activity log retrieval
 * - Simple read-only controller
 */
class ActivityLogController extends Controller
{
    /**
     * Display authenticated user's activity logs
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Get authenticated user's activity logs
        $logs = auth()->user()
            ->activityLogs()
            ->latest()
            ->paginate(20);

        // TODO Phase 10: Use ActivityLogResource::collection()
        return response()->json([
            'activity_logs' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ], 200);
    }
}
