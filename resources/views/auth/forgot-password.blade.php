<x-guest-layout>
    <!-- Info Text -->
    <div class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-6 text-center">
        Lupa kata sandi? Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-600 dark:text-emerald-400 font-semibold text-sm">
            {{ session('status') }}
        </div>
    @endif

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

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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
                       autofocus>
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none transition-colors"></i>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl text-white font-bold hover:shadow-lg hover:shadow-emerald-500/25 transition-all active:scale-98 cursor-pointer flex items-center justify-center gap-2">
            Kirim Tautan Reset <i class="fas fa-paper-plane text-sm"></i>
        </button>

        <!-- Back to Login Link -->
        <div class="text-center text-sm font-semibold text-slate-500 dark:text-slate-400">
            <a href="{{ route('login') }}" class="text-emerald-500 dark:text-emerald-400 hover:underline inline-flex items-center gap-1.5">
                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Login
            </a>
        </div>
    </form>
</x-guest-layout>
