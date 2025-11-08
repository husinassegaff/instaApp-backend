@props(['post'])

<div class="bg-white rounded-lg shadow-sm overflow-hidden"
     x-data="{
         liked: {{ $post->likes->where('user_id', auth()->id())->count() > 0 ? 'true' : 'false' }},
         likesCount: {{ $post->likes_count ?? 0 }},
         async toggleLike() {
             const wasLiked = this.liked;

             // Optimistic UI update
             this.liked = !this.liked;
             this.likesCount += this.liked ? 1 : -1;

             try {
                 const url = `/api/posts/{{ $post->id }}/${wasLiked ? 'unlike' : 'like'}`;
                 const response = await fetch(url, {
                     method: wasLiked ? 'DELETE' : 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                         'Accept': 'application/json'
                     }
                 });

                 if (!response.ok) {
                     // Revert on error
                     this.liked = wasLiked;
                     this.likesCount += wasLiked ? 1 : -1;
                     const error = await response.json();
                     console.error('Failed to toggle like:', error);
                 }
             } catch (error) {
                 // Revert on error
                 this.liked = wasLiked;
                 this.likesCount += wasLiked ? 1 : -1;
                 console.error('Failed to toggle like:', error);
             }
         }
     }">
    <!-- Post Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-100">
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
                <p class="text-xs text-gray-500">
                    {{ $post->created_at->diffForHumans() }}
                </p>
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
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
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

    <!-- Post Image -->
    <a href="{{ route('posts.show', $post->id) }}" class="block">
        <img src="{{ $post->image }}"
             alt="Post by {{ $post->user->name }}"
             class="w-full object-cover"
             style="max-height: 600px;">
    </a>

    <!-- Post Actions -->
    <div class="px-4 pt-3">
        <div class="flex items-center space-x-4">
            <!-- Like Button -->
            <button @click="toggleLike()"
                    :class="{ 'text-red-500': liked, 'text-gray-700 hover:text-red-500': !liked }"
                    class="flex items-center space-x-1 transition">
                <svg class="w-6 h-6"
                     :fill="liked ? 'currentColor' : 'none'"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="text-sm font-semibold" x-text="likesCount"></span>
            </button>

            <!-- Comment Button -->
            <a href="{{ route('posts.show', $post->id) }}" class="flex items-center space-x-1 text-gray-700 hover:text-purple-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-sm font-semibold">{{ $post->comments_count ?? 0 }}</span>
            </a>
        </div>
    </div>

    <!-- Caption -->
    @if($post->caption)
        <div class="px-4 pb-4 pt-2">
            <div x-data="{ expanded: false }" class="text-sm text-gray-800">
                <a href="{{ route('profile.show', $post->user->id) }}" class="font-semibold hover:text-purple-600">
                    {{ $post->user->name }}
                </a>
                <span x-show="!expanded && {{ strlen($post->caption) > 150 ? 'true' : 'false' }}">
                    {{ Str::limit($post->caption, 150) }}
                    <button @click="expanded = true" class="text-gray-500 hover:text-gray-700">
                        more
                    </button>
                </span>
                <span x-show="expanded || {{ strlen($post->caption) <= 150 ? 'true' : 'false' }}" style="{{ strlen($post->caption) <= 150 ? '' : 'display: none;' }}">
                    {{ $post->caption }}
                </span>
            </div>

            <!-- View Comments Link -->
            @if($post->comments_count > 0)
                <a href="{{ route('posts.show', $post->id) }}" class="text-sm text-gray-500 hover:text-gray-700 mt-1 inline-block">
                    View all {{ $post->comments_count }} {{ $post->comments_count === 1 ? 'comment' : 'comments' }}
                </a>
            @endif
        </div>
    @endif
</div>
