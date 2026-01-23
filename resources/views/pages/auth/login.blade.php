@extends('components.layout.app')

@section('title', 'Login - ' . site_name())

@section('og_title', 'Login - ' . site_name())
@section('og_description', 'Login - ' . site_name())
@section('og_url', url()->current())
@section('og_image', asset(site_setting('site_logo', 'img/icon_ABBI.png')))

@section('content')
    <div class="min-h-[calc(100vh-56px)] flex items-center justify-center bg-gray-50 px-4 py-10">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
            {{-- Brand Header --}}
            <div class="text-center mb-8">
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                    <span class="text-secondary">Ayo</span>buatbaik
                </h1>
            </div>

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email Field --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg
                               focus:outline-none focus:ring-2 focus:ring-secondary/40 focus:border-secondary
                               placeholder-gray-400 transition duration-200"
                        placeholder="Masukkan email" required>
                </div>

                {{-- Password Field + Eye Toggle --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-2 focus:ring-secondary/40 focus:border-secondary
                                   placeholder-gray-400 transition duration-200 pr-10"
                            placeholder="Masukkan password" required>

                        {{-- Eye Icon --}}
                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Login with Google --}}
                <a href="{{ route('oauth.google') }}"
                    class="w-full mt-3 flex items-center justify-center gap-2 border border-gray-300 text-gray-700 py-2.5 px-4 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5 h-5"
                        alt="Google logo">
                    Login dengan Google
                </a>


                {{-- Submit Button --}}
                <button type="submit"
                    class="w-full bg-secondary text-white py-2.5 px-4 rounded-lg font-semibold
                           hover:bg-goldDark hover:shadow-lg
                           focus:outline-none focus:ring-2 focus:ring-secondary/40 focus:ring-offset-1
                           transition-all duration-300 ease-in-out">
                    Masuk
                </button>
            </form>

            {{-- Footer Text --}}
            <div class="text-center mt-6 text-xs text-gray-400">
                &copy; {{ date('Y') }} Ayobuatbaik. All rights reserved.
            </div>
        </div>
    </div>

    {{-- Toggle Password Visibility Script --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("password");
            const icon = togglePassword.querySelector("i");

            togglePassword.addEventListener("click", () => {
                const isHidden = passwordInput.type === "password";
                passwordInput.type = isHidden ? "text" : "password";

                // Ganti ikon (mata terbuka ↔ mata tertutup)
                icon.classList.toggle("fa-eye");
                icon.classList.toggle("fa-eye-slash");
            });
        });
    </script>
@endsection
