<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\ActivityLoggerService;
use App\Services\AuthService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AuthController
 *
 * Handles authentication operations following SOLID principles:
 * - SRP: Only handles HTTP authentication logic
 * - DIP: Depends on AuthService and ActivityLoggerService (injected via constructor)
 */
class AuthController extends Controller
{
    /**
     * @param AuthService $authService
     * @param ActivityLoggerService $activityLogger
     */
    public function __construct(
        private AuthService $authService,
        private ActivityLoggerService $activityLogger
    ) {}

    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Validation handled by RegisterRequest (Single Responsibility Principle)
        $validated = $request->validated();

        // Use AuthService for business logic
        $user = $this->authService->register($validated);

        // Generate Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;

        // TODO Phase 10: Use UserResource for transformation
        return response()->json([
            'message' => 'Registration successful. Please verify your email.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login user and return token
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Validation handled by LoginRequest (Single Responsibility Principle)
        $validated = $request->validated();

        // Use AuthService for business logic (handles validation, email verification check, logging)
        $result = $this->authService->login($validated);

        // TODO Phase 10: Use UserResource
        return response()->json([
            'message' => 'Login successful.',
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
                'email_verified_at' => $result['user']->email_verified_at,
            ],
            'token' => $result['token'],
        ], 200);
    }

    /**
     * Logout user (revoke current token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Use AuthService for business logic
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logout successful.',
        ], 200);
    }

    /**
     * Verify user email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->route('id'));

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 200);
        }

        // Verify the email
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Use ActivityLoggerService for logging
        $this->activityLogger->log(
            user: $user,
            logName: 'auth',
            description: 'Email verified',
            subject: $user
        );

        return response()->json([
            'message' => 'Email verified successfully.',
        ], 200);
    }

    /**
     * Resend email verification notification
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent.',
        ], 200);
    }

    /**
     * Get authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        // TODO Phase 10: Use UserResource
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'bio' => $user->bio,
                'profile_image' => $user->profile_image,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }
}
