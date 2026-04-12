<x-dashboard-layout>
    @include('profile.partials.nav')

    <div class="bg-white border border-neutral-200 shadow-xl rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-8">
        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-2">
            {{ __('Profile Information') }}
        </h3>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">
            {{ __("Update your account's profile information and email address.") }}
        </p>

        <!-- Alert Message -->
        <x-alert-message />

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form id="form-element" method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div class="grid grid-cols-6 items-start gap-4">
                <div class="col-span-1 pt-2">
                    <x-input-label for="username">Username</x-input-label>
                </div>
                <div class="col-span-5">
                    <x-text-input id="username" class="block w-full" type="text" name="username" value="{{ $model->username }}" autocomplete="username" readonly />
                </div>
            </div>

            <div class="grid grid-cols-6 items-start gap-4">
                <div class="col-span-1 pt-2">
                    <x-input-label for="name">Name</x-input-label>
                </div>
                <div class="col-span-5">
                    <x-text-input id="name" class="block w-full" type="text" name="name" value="{{ $model->name }}" autocomplete="name" />
                </div>
            </div>

            <div class="grid grid-cols-6 items-start gap-4">
                <div class="col-span-1 pt-2">
                    <x-input-label for="email">Email</x-input-label>
                </div>
                <div class="col-span-5">
                    <x-text-input id="email" class="block w-full" type="email" name="email" value="{{ $model->email }}" autocomplete="email" readonly />
                </div>
            </div>

            <div class="grid grid-cols-6 items-start gap-4">
                <div class="col-span-1 pt-2">
                    <x-input-label for="current_password">Current Password</x-input-label>
                </div>
                <div class="col-span-5">
                    <x-text-input id="current_password" class="block w-full" type="password" name="current_password" autocomplete="current-password" />
                </div>
            </div>

            <div class="grid grid-cols-6 items-start gap-4">
                <div class="col-span-1 pt-2">
                    <x-input-label for="password">New Password</x-input-label>
                </div>
                <div class="col-span-5">
                    <x-text-input id="password" class="block w-full" type="password" name="password" autocomplete="password" />
                </div>
            </div>

            <div class="grid grid-cols-6 items-start gap-4">
                <div class="col-span-1 pt-2">
                    <x-input-label for="password_confirmation">Confirm New Password</x-input-label>
                </div>
                <div class="col-span-5">
                    <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" autocomplete="password_confirmation" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                    Save
                </button>
            </div>
        </form>
    </div>

    @push('script')
    <script type="module">
        $('#form-element').formAjaxSubmit();
    </script>
    @endpush
</x-dashboard-layout>
