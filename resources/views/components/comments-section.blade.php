@props(['post'])

<div x-data="{
    comments: {{ $post->comments->toJson() }},
    newComment: '',
    loading: false,
    async addComment() {
        if (!this.newComment.trim()) return;

        this.loading = true;

        try {
            const response = await fetch(`/api/posts/{{ $post->id }}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: this.newComment })
            });

            if (response.ok) {
                const data = await response.json();
                this.comments.push(data.data);
                this.newComment = '';
            } else {
                const error = await response.json();
                alert(error.message || 'Failed to add comment');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        } finally {
            this.loading = false;
        }
    },
    async deleteComment(commentId, index) {
        if (!confirm('Are you sure you want to delete this comment?')) return;

        try {
            const response = await fetch(`/api/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                this.comments.splice(index, 1);
            } else {
                const error = await response.json();
                alert(error.message || 'Failed to delete comment');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    }
}">
    <!-- Existing Comments -->
    <template x-for="(comment, index) in comments" :key="comment.id">
        <div class="flex space-x-3">
            <a :href="`/profile/${comment.user.username}`" class="flex-shrink-0">
                <template x-if="comment.user.profile_image">
                    <img :src="comment.user.profile_image"
                         :alt="comment.user.name"
                         class="w-8 h-8 rounded-full object-cover">
                </template>
                <template x-if="!comment.user.profile_image">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-semibold"
                         x-text="comment.user.name.charAt(0).toUpperCase()">
                    </div>
                </template>
            </a>
            <div class="flex-1">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <a :href="`/profile/${comment.user.username}`"
                           class="font-semibold text-sm hover:text-purple-600"
                           x-text="comment.user.name">
                        </a>
                        <span class="text-sm text-gray-800 ml-2" x-text="comment.content"></span>
                    </div>

                    <!-- Delete button (only for comment owner) -->
                    <button x-show="comment.user.id === {{ auth()->id() }}"
                            @click="deleteComment(comment.id, index)"
                            class="ml-2 text-red-500 hover:text-red-700 transition"
                            title="Delete comment">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1" x-text="new Date(comment.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })"></p>
            </div>
        </div>
    </template>

    <!-- Empty State -->
    <div x-show="comments.length === 0" class="text-center py-8 text-gray-500">
        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p class="text-sm">No comments yet</p>
        <p class="text-xs">Be the first to comment!</p>
    </div>

    <!-- Add Comment Form -->
    <div class="border-t border-gray-100 pt-4 mt-4">
        <form @submit.prevent="addComment()" class="flex items-center space-x-2">
            <input type="text"
                   id="comment-input"
                   x-model="newComment"
                   placeholder="Add a comment..."
                   :disabled="loading"
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                   @keydown.enter="addComment()">
            <button type="submit"
                    :disabled="!newComment.trim() || loading"
                    :class="{ 'opacity-50 cursor-not-allowed': !newComment.trim() || loading }"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold text-sm hover:bg-purple-700 transition disabled:hover:bg-purple-600">
                <span x-show="!loading">Post</span>
                <span x-show="loading">...</span>
            </button>
        </form>
    </div>
</div>
