<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-700 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot> --}}

    <div class="py-3">
        <div class="max-w-7xl w-full mx-auto sm:px-6 lg:px-8 pt-1 pb-2">
            <a href={{ route("dashboard") }} class="inline-flex gap-2 items-center text-gray-800 hover:bg-blue-100 rounded-md transition-colors duration-300">
                <i data-lucide="chevron-left" class="size-7"></i>
                <span>Kembali</span>
            </a>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:px-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
