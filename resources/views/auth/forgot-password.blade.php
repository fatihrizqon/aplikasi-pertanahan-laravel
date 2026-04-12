<x-auth-layout>
    <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-neutral-900 dark:border-neutral-700">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <div class="p-4 sm:p-7 flex flex-col">
            <div class="text-center">
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </div>
            </div>

            <form method="POST" action="{{ route('password.email') }}" class="grid gap-y-4 bg">
                @csrf

                <div>
                    <x-input-label for="email">Email</x-input-label>
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                </div>

                <div class="flex items-center justify-end mt-2">
                    <x-primary-button>
                        {{ __('Email Password Reset Link') }}
                    </x-primary-button>
                </div>
            </form>
            <div class="mt-4 text-sm text-gray-600 dark:text-neutral-400">
                <a class="text-blue-600 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium dark:text-blue-500" href="{{ route('login') }}">
                    Back to Sign in
                </a>
            </div>
        </div>
    </div>



</x-auth-layout>
