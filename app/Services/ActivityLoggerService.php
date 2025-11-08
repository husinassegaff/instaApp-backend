<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * ActivityLoggerService
 *
 * SOLID Principles:
 * - SRP: Single responsibility - handles ONLY activity logging
 * - OCP: Open for extension (can be extended for custom log types)
 * - DIP: Used by all other services (dependency inversion)
 *
 * This service is injected into all other services that need to log activities.
 */
class ActivityLoggerService
{
    /**
     * @param Request $request
     */
    public function __construct(
        private Request $request
    ) {}

    /**
     * Log an activity
     *
     * @param User|null $user User who performed the action (null for guest actions)
     * @param string $logName Category of log (auth, post, like, comment)
     * @param string $description Human-readable description
     * @param mixed $subject Optional model that was acted upon (Post, Comment, Like, etc.)
     * @param array $properties Additional metadata as array
     * @return ActivityLog
     */
    public function log(
        ?User $user,
        string $logName,
        string $description,
        mixed $subject = null,
        array $properties = []
    ): ActivityLog {
        $logData = [
            'user_id' => $user?->id,
            'log_name' => $logName,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ];

        // Handle polymorphic relationship if subject is provided
        if ($subject !== null) {
            $logData['subject_type'] = get_class($subject);
            $logData['subject_id'] = $subject->id;
        }

        return ActivityLog::create($logData);
    }
}
