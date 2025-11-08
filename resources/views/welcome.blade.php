<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'InstaApp') }} - Share Your Moments</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            {{ config('app.name', 'InstaApp') }}
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-2 rounded-lg font-medium hover:shadow-lg transition transform hover:scale-105">
                            Sign Up
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="py-20 lg:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="text-center lg:text-left">
                        <h2 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                            Share Your
                            <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                Moments
                            </span>
                        </h2>
                        <p class="text-xl text-gray-600 mb-8">
                            Connect with friends and the world around you. Share photos, like posts, and engage with a vibrant community.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:shadow-xl transition transform hover:scale-105">
                                Get Started
                            </a>
                            <a href="{{ route('login') }}" class="bg-white border-2 border-purple-600 text-purple-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-purple-50 transition">
                                Sign In
                            </a>
                        </div>
                    </div>

                    <!-- Right Content - Mock Instagram Feed -->
                    <div class="relative">
                        <div class="bg-white rounded-xl shadow-2xl p-6 border border-gray-200">
                            <!-- Mock Post -->
                            <div class="mb-4">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full mr-3"></div>
                                    <div>
                                        <div class="font-semibold text-gray-900">username</div>
                                        <div class="text-xs text-gray-500">2 hours ago</div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-purple-200 via-pink-200 to-yellow-200 h-64 rounded-lg mb-4 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex items-center space-x-4 mb-3">
                                    <button class="text-red-500 hover:text-red-600 transition">
                                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <button class="text-gray-600 hover:text-gray-700 transition">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="font-semibold text-sm text-gray-900 mb-1">1,234 likes</div>
                                <div class="text-sm text-gray-700">
                                    <span class="font-semibold">username</span> Beautiful day at the beach!
                                </div>
                            </div>
                        </div>

                        <!-- Floating Elements -->
                        <div class="absolute -top-4 -right-4 bg-purple-500 text-white px-4 py-2 rounded-full shadow-lg transform rotate-12">
                            <span class="font-bold">Free!</span>
                        </div>
                        <div class="absolute -bottom-4 -left-4 bg-pink-500 text-white px-4 py-2 rounded-full shadow-lg transform -rotate-12">
                            <span class="font-bold">Join Now</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h3 class="text-4xl font-bold text-gray-900 mb-4">Why Choose InstaApp?</h3>
                    <p class="text-xl text-gray-600">Everything you need to share and connect</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="text-center p-8 rounded-xl hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Share Photos</h4>
                        <p class="text-gray-600">Upload and share your favorite moments with beautiful images</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="text-center p-8 rounded-xl hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Like & Engage</h4>
                        <p class="text-gray-600">Show your appreciation with likes and meaningful comments</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="text-center p-8 rounded-xl hover:shadow-lg transition">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Build Community</h4>
                        <p class="text-gray-600">Connect with friends and discover new people with shared interests</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-r from-purple-600 to-pink-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h3 class="text-4xl font-bold text-white mb-6">Ready to Get Started?</h3>
                <p class="text-xl text-purple-100 mb-8">Join thousands of users already sharing their moments</p>
                <a href="{{ route('register') }}" class="inline-block bg-white text-purple-600 px-10 py-4 rounded-lg font-bold text-lg hover:shadow-2xl transition transform hover:scale-105">
                    Create Your Account
                </a>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-4">
                        {{ config('app.name', 'InstaApp') }}
                    </h1>
                    <p class="text-sm mb-4">&copy; {{ date('Y') }} {{ config('app.name', 'InstaApp') }}. All rights reserved.</p>
                    <p class="text-xs text-gray-500">Built with Laravel & Tailwind CSS</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
