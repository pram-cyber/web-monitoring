<x-guest-layout>
    <!-- Session Status (e.g. password resets success) -->
    @if (session('status'))
        <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-600 dark:text-emerald-400 font-semibold text-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Validation Errors Alert Box -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 rounded-xl text-sm flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-lg mt-0.5"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Input Group -->
        <div class="space-y-1.5">
            <label for="email" class="text-sm font-semibold text-slate-700 dark:text-slate-300 block">Alamat Email</label>
            <div class="relative">
                <input id="email" 
                       type="email" 
                       name="email" 
                       class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 font-medium focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200" 
                       placeholder="nama@email.com" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none transition-colors"></i>
            </div>
        </div>

        <!-- Password Input Group -->
        <div class="space-y-1.5">
            <div class="flex justify-between items-center">
                <label for="password" class="text-sm font-semibold text-slate-700 dark:text-slate-300 block">Kata Sandi</label>
            </div>
            <div class="relative">
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="w-full pl-12 pr-12 py-3.5 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 font-medium focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200" 
                       placeholder="••••••••" 
                       required 
                       autocomplete="current-password">
                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none transition-colors"></i>
                <button type="button" 
                        onclick="togglePasswordVisibility()" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer focus:outline-none">
                    <i class="fas fa-eye" id="password-eye-icon"></i>
                </button>
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between text-xs sm:text-sm">
            <label for="remember_me" class="flex items-center gap-2 cursor-pointer select-none text-slate-500 dark:text-slate-400 font-medium">
                <input id="remember_me" type="checkbox" class="w-4.5 h-4.5 rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 cursor-pointer accent-emerald-500" name="remember">
                <span>Ingat saya</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="text-emerald-500 dark:text-emerald-400 font-bold hover:underline" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif
        </div>

        <!-- Login Submit Button -->
        <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl text-white font-bold hover:shadow-lg hover:shadow-emerald-500/25 transition-all active:scale-98 cursor-pointer flex items-center justify-center gap-2">
            Masuk ke Dashboard <i class="fas fa-arrow-right text-sm"></i>
        </button>

        <!-- Register Link -->
        <div class="text-center text-sm font-medium text-slate-500 dark:text-slate-400">
            Belum punya akun? <a href="{{ route('register') }}" class="text-emerald-500 dark:text-emerald-400 font-bold hover:underline">Daftar di sini</a>
        </div>
    </form>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('password-eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</x-guest-layout>
