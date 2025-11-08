<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * WebAuthService
 *
 * Session-based authentication service for web application.
 * Separate from AuthService which handles API token-based authentication.
 *
 * SOLID Principles:
 * - SRP: Single responsibility - handles ONLY web authentication business logic
 * - DIP: Depends on ActivityLoggerService abstraction (injected)
 * - OCP: Open for extension (can add new auth methods without modifying existing)
 */
class WebAuthService
{
    /**
     * @param ActivityLoggerService $activityLogger
     */
    public function __construct(
        private ActivityLoggerService $activityLogger
    ) {}

    /**
     * Register a new user (without sending email verification)
     *
     * Note: Email verification should be sent by the controller after
     * successful registration to avoid duplication.
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
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'auth',
            description: 'User registered via web',
            subject: $user,
            properties: ['email' => $user->email]
        );

        return $user;
    }

    /**
     * Login user with session-based authentication
     *
     * @param array $credentials Login credentials (email, password, remember)
     * @return User
     * @throws ValidationException
     */
    public function login(array $credentials): User
    {
        // Extract remember me option
        $remember = $credentials['remember'] ?? false;

        // Prepare credentials for Auth::attempt
        $loginCredentials = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        // Find user by email first to check email verification
        $user = User::where('email', $credentials['email'])->first();

        // Validate user exists
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if password is correct
        if (! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Attempt to login with session
        if (! Auth::attempt($loginCredentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Regenerate session to prevent fixation attacks
        request()->session()->regenerate();

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'auth',
            description: 'User logged in via web',
            subject: $user
        );

        return $user;
    }

    /**
     * Logout user (session-based, no token deletion)
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        // Log activity BEFORE logout (so we still have the user)
        $this->activityLogger->log(
            user: $user,
            logName: 'auth',
            description: 'User logged out via web',
            subject: $user
        );

        // Logout from session
        Auth::logout();

        // Invalidate session
        request()->session()->invalidate();

        // Regenerate CSRF token
        request()->session()->regenerateToken();
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

        // Log activity
        $this->activityLogger->log(
            user: $user,
            logName: 'auth',
            description: 'Email verification sent',
            subject: $user,
            properties: ['email' => $user->email]
        );
    }
}
