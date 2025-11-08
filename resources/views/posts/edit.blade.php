@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Post</h2>
            <a href="{{ route('posts.show', $post->id) }}"
               class="text-gray-600 hover:text-gray-800 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('posts.update', $post->id) }}"
              x-data="{
                  imagePreview: '{{ $post->image }}',
                  imageBase64: '',
                  imageChanged: false,
                  loading: false,
                  handleImageUpload(event) {
                      const file = event.target.files[0];
                      if (file) {
                          // Validate file size (max 5MB)
                          if (file.size > 5 * 1024 * 1024) {
                              alert('Image size must be less than 5MB');
                              event.target.value = '';
                              return;
                          }

                          // Validate file type
                          if (!file.type.startsWith('image/')) {
                              alert('Please select a valid image file');
                              event.target.value = '';
                              return;
                          }

                          // Create preview
                          const reader = new FileReader();
                          reader.onload = (e) => {
                              this.imagePreview = e.target.result;
                              this.imageBase64 = e.target.result;
                              this.imageChanged = true;
                          };
                          reader.readAsDataURL(file);
                      }
                  },
                  resetImage() {
                      this.imagePreview = '{{ $post->image }}';
                      this.imageBase64 = '';
                      this.imageChanged = false;
                      document.getElementById('image-input').value = '';
                  }
              }">
            @csrf
            @method('PUT')

            <!-- Current Image -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Photo
                </label>

                <!-- Image Preview -->
                <div class="mb-4 relative">
                    <img :src="imagePreview" alt="Post image" class="w-full rounded-lg shadow-sm" style="max-height: 500px; object-fit: contain;">

                    <!-- Change Image Button -->
                    <button type="button"
                            @click="$refs.fileInput.click()"
                            class="absolute bottom-4 right-4 bg-white text-gray-700 px-4 py-2 rounded-lg shadow-lg hover:bg-gray-100 transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Change Photo</span>
                    </button>

                    <!-- Reset Button (only shown when image changed) -->
                    <button type="button"
                            x-show="imageChanged"
                            @click="resetImage()"
                            class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition shadow-lg"
                            style="display: none;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <input type="file"
                       id="image-input"
                       x-ref="fileInput"
                       @change="handleImageUpload($event)"
                       accept="image/*"
                       class="hidden">

                <!-- Hidden input for base64 data (only sent if image changed) -->
                <input type="hidden" name="image" x-model="imageBase64" x-show="imageChanged">

                @error('image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Caption -->
            <div class="mb-6">
                <label for="caption" class="block text-sm font-medium text-gray-700 mb-2">
                    Caption
                </label>
                <textarea id="caption"
                          name="caption"
                          rows="4"
                          placeholder="Write a caption..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none @error('caption') border-red-500 @enderror">{{ old('caption', $post->caption) }}</textarea>
                @error('caption')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
                <button type="submit"
                        :disabled="loading"
                        :class="{ 'opacity-50 cursor-not-allowed': loading }"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition transform hover:scale-105 disabled:transform-none disabled:hover:shadow-none">
                    <span x-show="!loading">Update Post</span>
                    <span x-show="loading" class="flex items-center justify-center" style="display: none;">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Updating...
                    </span>
                </button>

                <a href="{{ route('posts.show', $post->id) }}"
                   class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
