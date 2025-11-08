<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\WebAuthService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly WebAuthService $authService
    ) {}

    /**
     * Show login form
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            // Use WebAuthService to validate and authenticate
            $this->authService->login($request->validated());

            return redirect()->intended(route('feed'))->with('success', 'Welcome back!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->onlyInput('email');
        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error('Login failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->only('email')
            ]);

            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ])->onlyInput('email');
        }
    }

    /**
     * Show registration form
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            // Use WebAuthService to create user
            $user = $this->authService->register($request->validated());

            // Send email verification (only once, handled here)
            $this->authService->sendVerificationEmail($user);

            // Log the user in with session
            Auth::login($user);

            // Regenerate session for security
            $request->session()->regenerate();

            return redirect()->route('verification.notice')->with('success', 'Registration successful! Please verify your email address.');
        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password', 'password_confirmation'])
            ]);

            return back()->withErrors([
                'email' => 'An error occurred during registration. Please try again.',
            ])->withInput();
        }
    }

    /**
     * Show email verification notice
     */
    public function verificationNotice(): View|RedirectResponse
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('feed')->with('info', 'Your email is already verified.');
        }

        return view('auth.verify-email');
    }

    /**
     * Handle email verification
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('feed')->with('info', 'Your email is already verified.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('feed')->with('success', 'Your email has been verified!');
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('feed');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link has been sent to your email!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): RedirectResponse
    {
        // Use WebAuthService to handle logout (logs activity and clears session)
        $this->authService->logout(Auth::user());

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
}
