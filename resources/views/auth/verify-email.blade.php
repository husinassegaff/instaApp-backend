@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
<div x-data="{
    canResend: true,
    countdown: 0,
    resendEmail() {
        if (!this.canResend) return;

        this.canResend = false;
        this.countdown = 60;

        // Submit the resend form
        this.$refs.resendForm.submit();

        // Start countdown
        const timer = setInterval(() => {
            this.countdown--;
            if (this.countdown <= 0) {
                this.canResend = true;
                clearInterval(timer);
            }
        }, 1000);
    }
}">
    <!-- Icon -->
    <div class="flex justify-center mb-6">
        <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
    </div>

    <!-- Title -->
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-4">
        Verify Your Email
    </h2>

    <!-- Description -->
    <p class="text-center text-gray-600 mb-6">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
    </p>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-blue-800">
            <strong>Check your inbox:</strong> We've sent a verification link to <strong>{{ auth()->user()->email }}</strong>
        </p>
    </div>

    <!-- Resend Form (Hidden) -->
    <form ref="resendForm" method="POST" action="{{ route('verification.send') }}" class="hidden">
        @csrf
    </form>

    <!-- Resend Button -->
    <button
        @click="resendEmail"
        :disabled="!canResend"
        :class="{ 'opacity-50 cursor-not-allowed': !canResend }"
        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition transform hover:scale-105 disabled:transform-none disabled:hover:shadow-none">
        <span x-show="canResend">Resend Verification Email</span>
        <span x-show="!canResend" x-text="'Resend in ' + countdown + 's'" style="display: none;"></span>
    </button>

    <!-- Divider -->
    <div class="mt-6 flex items-center">
        <div class="flex-1 border-t border-gray-300"></div>
        <span class="px-4 text-sm text-gray-500">OR</span>
        <div class="flex-1 border-t border-gray-300"></div>
    </div>

    <!-- Logout Link -->
    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="w-full text-center text-sm text-gray-600 hover:text-gray-800">
            Log out and use a different account
        </button>
    </form>

    <!-- Help Text -->
    <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">
            Didn't receive the email? Check your spam folder or
            <a href="#" class="text-purple-600 hover:text-purple-700 underline">contact support</a>
        </p>
    </div>
</div>
@endsection
