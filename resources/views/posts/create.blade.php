@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Create New Post</h2>
            <a href="{{ route('feed') }}"
               class="text-gray-600 hover:text-gray-800 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <strong>There were errors with your submission:</strong>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('posts.store') }}"
              x-data="{
                  imagePreview: null,
                  imageBase64: '',
                  loading: false,
                  handleImageUpload(event) {
                      const file = event.target.files[0];
                      console.log('File selected:', file);

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

                          console.log('File validation passed, reading file...');

                          // Create preview
                          const reader = new FileReader();
                          reader.onload = (e) => {
                              console.log('File read complete, base64 length:', e.target.result.length);
                              this.imagePreview = e.target.result;
                              this.imageBase64 = e.target.result;
                              console.log('Image preview and base64 set');
                          };
                          reader.onerror = (e) => {
                              console.error('Error reading file:', e);
                              alert('Failed to read image file. Please try again.');
                          };
                          reader.readAsDataURL(file);
                      }
                  },
                  removeImage() {
                      this.imagePreview = null;
                      this.imageBase64 = '';
                      document.getElementById('image-input').value = '';
                  },
                  submitForm(event) {
                      if (!this.imageBase64) {
                          event.preventDefault();
                          alert('Please select an image to upload');
                          return;
                      }
                      console.log('Form submitting with image base64 length:', this.imageBase64.length);
                      this.loading = true;
                      // Let form submit naturally with CSRF token
                  }
              }"
              @submit="submitForm">
            @csrf

            <!-- Image Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Photo <span class="text-red-500">*</span>
                </label>

                <!-- Image Preview -->
                <div x-show="imagePreview" class="mb-4 relative" style="display: none;">
                    <img :src="imagePreview" alt="Preview" class="w-full rounded-lg shadow-sm" style="max-height: 500px; object-fit: contain;">
                    <button type="button"
                            @click="removeImage()"
                            class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Upload Area -->
                <div x-show="!imagePreview"
                     class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center hover:border-purple-400 transition cursor-pointer"
                     @click="$refs.fileInput.click()">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-600 mb-2">Click to upload an image</p>
                    <p class="text-sm text-gray-500">PNG, JPG, GIF up to 5MB</p>
                </div>

                <input type="file"
                       id="image-input"
                       x-ref="fileInput"
                       @change="handleImageUpload($event)"
                       accept="image/*"
                       class="hidden">

                <!-- Hidden input for base64 data -->
                <input type="hidden" name="image" x-model="imageBase64">

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
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none @error('caption') border-red-500 @enderror">{{ old('caption') }}</textarea>
                @error('caption')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
                <button type="submit"
                        :disabled="!imageBase64 || loading"
                        :class="{ 'opacity-50 cursor-not-allowed': !imageBase64 || loading }"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition transform hover:scale-105 disabled:transform-none disabled:hover:shadow-none">
                    <span x-show="!loading">Create Post</span>
                    <span x-show="loading" class="flex items-center justify-center" style="display: none;">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>

                <a href="{{ route('feed') }}"
                   class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
