<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-3 sm:mb-4" :status="session('status')" />

    <div class="space-y-4 sm:space-y-6">
        <!-- Welcome Header -->
        <div class="text-center mb-4 sm:mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1 sm:mb-2">Welcome Back</h2>
            <p class="text-sm text-gray-600">Sign in to your account</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5">
            @csrf

            <!-- Email Address -->
            <div class="space-y-1 sm:space-y-2">
                <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-gray-700" />
                <div class="relative">
                    <x-text-input id="email"
                        class="block w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 bg-gray-50 focus:bg-white"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Enter your email" />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs sm:text-sm" />
            </div>

            <!-- Password -->
            <div class="space-y-1 sm:space-y-2">
                <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700" />
                <div class="relative">
                    <x-text-input id="password"
                        class="block w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 bg-gray-50 focus:bg-white pr-10"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password" />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs sm:text-sm" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="w-4 h-4 rounded border-2 border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 focus:ring-2 transition-colors duration-200"
                        name="remember">
                    <span class="ml-2 text-xs sm:text-sm text-gray-600 font-medium">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                <a class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-semibold transition-colors duration-200 hover:underline"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
                @endif
            </div>

            <!-- Login Button -->
            <div class="pt-2 sm:pt-4">
                <button type="submit"
                    class="w-full flex justify-center items-center px-4 py-3 sm:py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-sm sm:text-base rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300 active:scale-95">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    {{ __('Sign In') }}
                </button>
            </div>

            <!-- Register Link -->
            @if (Route::has('register'))
            <div class="text-center pt-2 sm:pt-4 border-t border-gray-200">
                <p class="text-xs sm:text-sm text-gray-600">
                    Don't have an account?
                    <!-- <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors duration-200 hover:underline">
                        Sign up here
                    </a> -->
                </p>
            </div>
            @endif
        </form>
    </div>
</x-guest-layout>