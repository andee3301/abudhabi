<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">API Access Token</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Generate a Sanctum personal access token for API and docs usage. Tokens are shown once—copy
                            and store it securely.
                        </p>
                    </div>

                    @if (session('token_plain'))
                        <div class="rounded-md bg-gray-50 border border-gray-200 p-4 space-y-2">
                            <p class="text-sm font-semibold text-gray-800">New token (shown once):</p>
                            <p class="text-sm font-mono break-all text-gray-900">{{ session('token_plain') }}</p>
                            <p class="text-xs text-gray-600">Add to the docs Authorize modal as the raw token (no "Bearer "
                                prefix needed).</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.api-token.store') }}" class="space-y-4">
                        @csrf
                        <div class="space-y-1">
                            <label for="token_name" class="block text-sm font-medium text-gray-700">Token name</label>
                            <input id="token_name" name="token_name" type="text"
                                value="{{ old('token_name', 'API Token') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="e.g. Docs testing token" />
                            @error('token_name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-600">Tokens include all abilities by default.</p>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Generate token
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
