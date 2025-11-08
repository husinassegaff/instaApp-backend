<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthService
 *
 * SOLID Principles:
 * - SRP: Single responsibility - handles ONLY authentication business logic
 * - DIP: Depends on ActivityLoggerService abstraction (injected)
 * - OCP: Open for extension (can add new auth methods without modifying existing)
 */
class AuthService
{
    /**
     * @param ActivityLoggerService $activityLogger
     */
    public function __construct(
        private ActivityLoggerService $activityLogger
    ) {}

    /**
     * Register a new user
     *
     * @param array $data Validated registration data
     * @return User
     */
    public function register(array $data): User
    {
        // Create user with hashed password
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'auth',
            description: 'User registered',
            subject: $user,
            properties: ['email' => $user->email]
        );

        return $user;
    }

    /**
     * Login user and generate token
     *
     * @param array $credentials Login credentials (email, password)
     * @return array Returns array with 'user' and 'token'
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        // Find user by email
        $user = User::where('email', $credentials['email'])->first();

        // Validate credentials
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if email is verified
        if (! $user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Please verify your email before logging in.'],
            ]);
        }

        // Generate Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'auth',
            description: 'User logged in',
            subject: $user
        );

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout user (revoke current token)
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        // Revoke current access token
        $user->currentAccessToken()->delete();

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'auth',
            description: 'User logged out',
            subject: $user
        );
    }

    /**
     * Send email verification notification
     *
     * @param User $user
     * @return void
     */
    public function sendVerificationEmail(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }
}
