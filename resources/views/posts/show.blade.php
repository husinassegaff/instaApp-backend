@extends('layouts.app')

@section('title', 'Post by ' . $post->user->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('feed') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Feed
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-3 gap-0">
            <!-- Post Image (Left Side) -->
            <div class="md:col-span-2 bg-black flex items-center justify-center">
                <img src="{{ $post->image }}"
                     alt="Post by {{ $post->user->name }}"
                     class="w-full h-auto object-contain"
                     style="max-height: 700px;">
            </div>

            <!-- Post Details (Right Side) -->
            <div class="md:col-span-1 flex flex-col max-h-[700px]">
                <!-- Post Header -->
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- User Avatar -->
                            <a href="{{ route('profile.show', $post->user->id) }}" class="flex-shrink-0">
                                @if($post->user->profile_image)
                                    <img src="{{ $post->user->profile_image }}"
                                         alt="{{ $post->user->name }}"
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </a>

                            <!-- User Info -->
                            <div>
                                <a href="{{ route('profile.show', $post->user->id) }}"
                                   class="font-semibold text-gray-900 hover:text-purple-600 transition">
                                    {{ $post->user->name }}
                                </a>
                            </div>
                        </div>

                        <!-- Post Menu (Edit/Delete for owner) -->
                        @if(auth()->id() === $post->user_id)
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                        @click.away="open = false"
                                        class="p-2 hover:bg-gray-100 rounded-full transition">
                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open"
                                     x-transition
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                                     style="display: none;">
                                    <a href="{{ route('posts.edit', $post->id) }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Post
                                    </a>
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this post?')"
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete Post
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Comments Section (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <!-- Caption as First Comment -->
                    @if($post->caption)
                        <div class="flex space-x-3">
                            <a href="{{ route('profile.show', $post->user->id) }}" class="flex-shrink-0">
                                @if($post->user->profile_image)
                                    <img src="{{ $post->user->profile_image }}"
                                         alt="{{ $post->user->name }}"
                                         class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </a>
                            <div class="flex-1">
                                <div>
                                    <a href="{{ route('profile.show', $post->user->id) }}"
                                       class="font-semibold text-sm hover:text-purple-600">
                                        {{ $post->user->name }}
                                    </a>
                                    <span class="text-sm text-gray-800 ml-2">{{ $post->caption }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Comments -->
                    <x-comments-section :post="$post" />
                </div>

                <!-- Post Actions -->
                <div class="border-t border-gray-100 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <!-- Like Button -->
                            <button class="flex items-center space-x-1 text-gray-700 hover:text-red-500 transition">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>

                            <!-- Comment Button -->
                            <button onclick="document.getElementById('comment-input').focus()"
                                    class="flex items-center space-x-1 text-gray-700 hover:text-purple-600 transition">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Likes Count -->
                    <p class="font-semibold text-sm mb-2">{{ $post->likes->count() }} {{ $post->likes->count() === 1 ? 'like' : 'likes' }}</p>

                    <!-- Timestamp -->
                    <p class="text-xs text-gray-500 uppercase">{{ $post->created_at->format('F j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
