<x-auth-layout>
    <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-neutral-900 dark:border-neutral-700">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Sign Up</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                    Already have an account?
                    <a class="text-blue-600 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium dark:text-blue-500" href="{{ route('login') }}">
                        Sign in
                    </a>
                </p>
            </div>
            <div class="mt-5">
                <form id="form-element" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Username -->
                    <div>
                        <x-input-label for="username">Username</x-input-label>
                        <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" autofocus autocomplete="username" />
                    </div>

                    <!-- Name -->
                    <div class="mt-4">
                        <x-input-label for="name">Name</x-input-label>
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autocomplete="name" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4">
                        <x-input-label for="email">Email</x-input-label>
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="username" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password">Password</x-input-label>

                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <x-input-label for="password_confirmation">Confirm Password</x-input-label>

                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                    </div>

                    <div class="flex items-center mt-4">
                        <x-primary-button>
                            Register
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('script')
    <script type="module">
        // jsonScriptToFormFields('#form-element', '#data-json');
        $('#form-element').formAjaxSubmit();
    </script>
    @endsection
</x-auth-layout>
