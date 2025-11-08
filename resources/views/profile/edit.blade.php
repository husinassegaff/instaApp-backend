@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Profile</h2>
            <a href="{{ route('profile.show', auth()->user()->id) }}"
               class="text-gray-600 hover:text-gray-800 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('profile.update') }}"
              x-data="{
                  imagePreview: '{{ auth()->user()->profile_image }}',
                  imageBase64: '',
                  imageChanged: false,
                  loading: false,
                  handleImageUpload(event) {
                      const file = event.target.files[0];
                      if (file) {
                          // Validate file size (max 2MB)
                          if (file.size > 2 * 1024 * 1024) {
                              alert('Image size must be less than 2MB');
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
                      this.imagePreview = '{{ auth()->user()->profile_image }}';
                      this.imageBase64 = '';
                      this.imageChanged = false;
                      document.getElementById('image-input').value = '';
                  }
              }">
            @csrf
            @method('PUT')

            <!-- Profile Picture -->
            <div class="mb-6 text-center">
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    Profile Picture
                </label>

                <!-- Current/Preview Image -->
                <div class="flex justify-center mb-4">
                    <template x-if="imagePreview">
                        <img :src="imagePreview"
                             alt="Profile preview"
                             class="w-32 h-32 rounded-full object-cover border-4 border-purple-100">
                    </template>
                    <template x-if="!imagePreview">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-4xl border-4 border-purple-100">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </template>
                </div>

                <!-- Upload/Change Button -->
                <div class="flex justify-center space-x-2">
                    <button type="button"
                            @click="$refs.fileInput.click()"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">
                        {{ auth()->user()->profile_image ? 'Change Photo' : 'Upload Photo' }}
                    </button>

                    <button type="button"
                            x-show="imageChanged"
                            @click="resetImage()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
                            style="display: none;">
                        Reset
                    </button>
                </div>

                <input type="file"
                       id="image-input"
                       x-ref="fileInput"
                       @change="handleImageUpload($event)"
                       accept="image/*"
                       class="hidden">

                <!-- Hidden input for base64 data -->
                <input type="hidden" name="profile_image" x-model="imageBase64" x-show="imageChanged">

                @error('profile_image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bio -->
            <div class="mb-6">
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                    Bio
                </label>
                <textarea id="bio"
                          name="bio"
                          rows="4"
                          placeholder="Tell us about yourself..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none @error('bio') border-red-500 @enderror">{{ old('bio', $user->bio) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Maximum 500 characters</p>
                @error('bio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
                <button type="submit"
                        :disabled="loading"
                        :class="{ 'opacity-50 cursor-not-allowed': loading }"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition transform hover:scale-105 disabled:transform-none disabled:hover:shadow-none">
                    <span x-show="!loading">Save Changes</span>
                    <span x-show="loading" class="flex items-center justify-center" style="display: none;">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>

                <a href="{{ route('profile.show', $user->id) }}"
                   class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
