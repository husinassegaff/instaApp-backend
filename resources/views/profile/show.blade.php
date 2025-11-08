@extends('layouts.app')

@section('title', $user->name . ' (@' . $user->username . ')')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-8">
            <!-- Profile Image -->
            <div class="flex-shrink-0">
                @if($user->profile_image)
                    <img src="{{ $user->profile_image }}"
                         alt="{{ $user->name }}"
                         class="w-32 h-32 rounded-full object-cover border-4 border-purple-100">
                @else
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-4xl border-4 border-purple-100">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Profile Info -->
            <div class="flex-1">
                <div class="flex items-center space-x-4 mb-4">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                    @if(auth()->id() === $user->id)
                        <a href="{{ route('profile.edit') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                            Edit Profile
                        </a>
                    @endif
                </div>

                <p class="text-gray-600 mb-3">{{ '@' . $user->username }}</p>

                <!-- Stats -->
                <div class="flex items-center space-x-6 mb-4">
                    <div>
                        <span class="font-bold text-gray-900">{{ $user->posts()->count() }}</span>
                        <span class="text-gray-600">{{ $user->posts()->count() === 1 ? 'post' : 'posts' }}</span>
                    </div>
                </div>

                <!-- Bio -->
                @if($user->bio)
                    <p class="text-gray-800">{{ $user->bio }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Posts Grid -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Posts</h2>

        @if($posts->count() > 0)
            <div class="grid grid-cols-3 gap-1 md:gap-4">
                @foreach($posts as $post)
                    <a href="{{ route('posts.show', $post->id) }}"
                       class="relative aspect-square overflow-hidden rounded-lg group">
                        <img src="{{ $post->image }}"
                             alt="Post by {{ $user->name }}"
                             class="w-full h-full object-cover transition group-hover:opacity-90">

                        <!-- Overlay on hover -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <div class="flex items-center space-x-6 text-white">
                                <!-- Likes -->
                                <div class="flex items-center space-x-1">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    <span class="font-semibold">{{ $post->likes_count }}</span>
                                </div>

                                <!-- Comments -->
                                <div class="flex items-center space-x-1">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span class="font-semibold">{{ $post->comments_count }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Posts Yet</h3>
                <p class="text-gray-500 mb-6">
                    @if(auth()->id() === $user->id)
                        Start sharing your moments!
                    @else
                        {{ $user->name }} hasn't posted anything yet.
                    @endif
                </p>
                @if(auth()->id() === $user->id)
                    <a href="{{ route('posts.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:shadow-lg transition transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Your First Post
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
