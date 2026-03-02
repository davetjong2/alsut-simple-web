<section 
    x-data="{ editingPhoto: false }"
    @keydown.escape.window="editingPhoto = false"
    class="flex flex-col gap-5"
>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <div class="flex gap-10 w-full">

        {{-- RIGHT SIDE: PROFILE FORM --}}
        <div class="w-2/3">
            <form 
                method="POST" 
                action="{{ route('profile.update') }}" 
                enctype="multipart/form-data"
                class="flex flex-col gap-5"
            >
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input 
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('name', $user->name)"
                        required
                    />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input 
                        id="email"
                        name="email"
                        type="email"
                        class="mt-1 block w-full"
                        :value="old('email', $user->email)"
                        required
                    />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <div class="flex">
                    <x-primary-button class="w-auto px-6">
                        Save
                    </x-primary-button>
                </div>

                @if (session('status') === 'profile-updated')
                    <p 
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                @endif

                {{-- MODAL --}}
                <div 
                    x-show="editingPhoto"
                    x-transition
                    x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                >
                    <div 
                        class="bg-white p-6 rounded-lg w-96"
                        @click.away="editingPhoto = false"
                    >
                        <h3 class="text-lg font-medium mb-4">
                            Change Profile Picture
                        </h3>

                        <input 
                            name="profile_picture"
                            type="file"
                            class="mb-4 w-full"
                        />

                        <x-input-error :messages="$errors->get('profile_picture')" />

                        <div class="flex justify-end gap-2">
                            <button 
                                type="button"
                                @click="editingPhoto = false"
                                class="px-4 py-2 border rounded"
                            >
                                Cancel
                            </button>

                            <button 
                                type="submit"
                                @click="editingPhoto = false"
                                class="px-4 py-2 bg-blue-600 text-white rounded"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- LEFT SIDE: PHOTO --}}
        <div class="w-1/3 flex flex-col items-center">
            <div class="mb-4">
                <img 
                    class="h-40 w-40 rounded-full object-cover" 
                    src="{{ $user->profile_picture_url }}" 
                    alt="{{ $user->name }}"
                >
            </div>

            <div class="inline-flex gap-2 text-sm font-medium">
                <button
                    type="button"
                    @click="editingPhoto = true"
                    class="text-blue-600 hover:underline"
                >
                    {{ $user->profile_picture ? 'Edit' : 'Upload' }}
                </button>

                @if ($user->profile_picture)
                    <span class="text-gray-400">|</span>

                    <form method="POST" action="{{ route('profile.picture.delete') }}">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit"
                            class="text-red-600 hover:underline"
                            onclick="return confirm('Delete profile picture?')"
                        >
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>