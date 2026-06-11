<x-guest-layout>
    <!-- Validation Errors -->
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

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Input Group -->
        <div class="space-y-1.5">
            <label for="email" class="text-sm font-semibold text-slate-700 dark:text-slate-300 block">Alamat Email</label>
            <div class="relative">
                <input id="email" 
                       type="email" 
                       name="email" 
                       class="w-full pl-12 pr-4 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 font-medium focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200" 
                       placeholder="nama@email.com" 
                       value="{{ old('email', $request->email) }}" 
                       required 
                       autofocus 
                       autocomplete="username">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none transition-colors"></i>
            </div>
        </div>

        <!-- New Password Input Group -->
        <div class="space-y-1.5">
            <label for="password" class="text-sm font-semibold text-slate-700 dark:text-slate-300 block">Kata Sandi Baru</label>
            <div class="relative">
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="w-full pl-12 pr-12 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 font-medium focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200" 
                       placeholder="Minimal 8 karakter" 
                       required 
                       autocomplete="new-password">
                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none transition-colors"></i>
                <button type="button" 
                        onclick="togglePw('password', 'pw-eye')" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer focus:outline-none">
                    <i class="fas fa-eye" id="pw-eye"></i>
                </button>
            </div>
        </div>

        <!-- Confirm Password Input Group -->
        <div class="space-y-1.5">
            <label for="password_confirmation" class="text-sm font-semibold text-slate-700 dark:text-slate-300 block">Konfirmasi Kata Sandi</label>
            <div class="relative">
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       class="w-full pl-12 pr-12 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 font-medium focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200" 
                       placeholder="Ulangi kata sandi baru" 
                       required 
                       autocomplete="new-password">
                <i class="fas fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none transition-colors"></i>
                <button type="button" 
                        onclick="togglePw('password_confirmation', 'pw-c-eye')" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer focus:outline-none">
                    <i class="fas fa-eye" id="pw-c-eye"></i>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3.5 mt-2 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl text-white font-bold hover:shadow-lg hover:shadow-emerald-500/25 transition-all active:scale-98 cursor-pointer flex items-center justify-center gap-2">
            Reset Kata Sandi <i class="fas fa-key text-sm"></i>
        </button>

        <!-- Back to Login Link -->
        <div class="text-center text-sm font-semibold text-slate-500 dark:text-slate-400 pt-2">
            <a href="{{ route('login') }}" class="text-emerald-500 dark:text-emerald-400 hover:underline inline-flex items-center gap-1.5">
                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Login
            </a>
        </div>
    </form>

    <script>
        function togglePw(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</x-guest-layout>
