@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div x-data="{
    showPassword: false,
    showPasswordConfirmation: false,
    loading: false,
    password: '',
    passwordConfirmation: '',
    agreedToTerms: false,
    get passwordStrength() {
        if (this.password.length === 0) return { level: 0, text: '', color: '' };
        let strength = 0;
        if (this.password.length >= 8) strength++;
        if (/[a-z]/.test(this.password)) strength++;
        if (/[A-Z]/.test(this.password)) strength++;
        if (/[0-9]/.test(this.password)) strength++;
        if (/[^a-zA-Z0-9]/.test(this.password)) strength++;

        if (strength <= 2) return { level: 1, text: 'Weak', color: 'bg-red-500' };
        if (strength <= 3) return { level: 2, text: 'Medium', color: 'bg-yellow-500' };
        return { level: 3, text: 'Strong', color: 'bg-green-500' };
    },
    get passwordsMatch() {
        if (this.passwordConfirmation.length === 0) return null;
        return this.password === this.passwordConfirmation;
    },
    submitForm(event) {
        if (!this.agreedToTerms) {
            event.preventDefault();
            alert('Please agree to the terms and conditions');
            return;
        }
        this.loading = true;
        // Let form submit naturally with CSRF token
    }
}">
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">
        Create Your Account
    </h2>

    <form method="POST" action="{{ route('register') }}" @submit="submitForm" class="space-y-4">
        @csrf

        <!-- Name Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Full Name
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email Address
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Username Field -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                Username
            </label>
            <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                required
                autocomplete="username"
                placeholder="Choose a unique username"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('username') border-red-500 @enderror">
            @error('username')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">This will be your unique identifier on the platform</p>
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password
            </label>
            <div class="relative">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    name="password"
                    x-model="password"
                    required
                    autocomplete="new-password"
                    class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror">

                <!-- Show/Hide Password Toggle -->
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <!-- Eye Icon -->
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <!-- Eye Slash Icon -->
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>

            <!-- Password Strength Indicator -->
            <div x-show="password.length > 0" class="mt-2" style="display: none;">
                <div class="flex items-center space-x-2">
                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div
                            :class="passwordStrength.color"
                            class="h-full transition-all duration-300"
                            :style="'width: ' + (passwordStrength.level * 33.33) + '%'">
                        </div>
                    </div>
                    <span :class="{
                        'text-red-600': passwordStrength.level === 1,
                        'text-yellow-600': passwordStrength.level === 2,
                        'text-green-600': passwordStrength.level === 3
                    }" class="text-xs font-medium" x-text="passwordStrength.text"></span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Use 8+ characters with mix of letters, numbers & symbols</p>
            </div>

            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Confirmation Field -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm Password
            </label>
            <div class="relative">
                <input
                    :type="showPasswordConfirmation ? 'text' : 'password'"
                    id="password_confirmation"
                    name="password_confirmation"
                    x-model="passwordConfirmation"
                    required
                    autocomplete="new-password"
                    :class="{
                        'border-green-500': passwordsMatch === true,
                        'border-red-500': passwordsMatch === false
                    }"
                    class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">

                <!-- Show/Hide Password Toggle -->
                <button
                    type="button"
                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <!-- Eye Icon -->
                    <svg x-show="!showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <!-- Eye Slash Icon -->
                    <svg x-show="showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>

            <!-- Password Match Indicator -->
            <div x-show="passwordConfirmation.length > 0" style="display: none;">
                <p x-show="passwordsMatch === true" class="mt-1 text-sm text-green-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Passwords match
                </p>
                <p x-show="passwordsMatch === false" class="mt-1 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Passwords do not match
                </p>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="flex items-start">
            <input
                type="checkbox"
                id="terms"
                x-model="agreedToTerms"
                class="w-4 h-4 mt-1 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
            <label for="terms" class="ml-2 text-sm text-gray-600">
                I agree to the
                <a href="#" class="text-purple-600 hover:text-purple-700 underline">Terms and Conditions</a>
                and
                <a href="#" class="text-purple-600 hover:text-purple-700 underline">Privacy Policy</a>
            </label>
        </div>

        <!-- Register Button -->
        <button
            type="submit"
            :disabled="loading || !agreedToTerms"
            :class="{ 'opacity-50 cursor-not-allowed': loading || !agreedToTerms }"
            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition transform hover:scale-105 disabled:transform-none disabled:hover:shadow-none">
            <span x-show="!loading">Create Account</span>
            <span x-show="loading" class="flex items-center justify-center" style="display: none;">
                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating account...
            </span>
        </button>
    </form>

    <!-- Divider -->
    <div class="mt-6 flex items-center">
        <div class="flex-1 border-t border-gray-300"></div>
        <span class="px-4 text-sm text-gray-500">OR</span>
        <div class="flex-1 border-t border-gray-300"></div>
    </div>

    <!-- Login Link -->
    <p class="mt-6 text-center text-sm text-gray-600">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-purple-600 hover:text-purple-700">
            Log in
        </a>
    </p>
</div>
@endsection
