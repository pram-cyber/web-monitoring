<x-guest-layout>
    <!-- Info Text -->
    <div style="font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px; text-align: center;">
        Lupa kata sandi? Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-4" style="border-radius: 12px; font-weight: 550; font-size: 14px">
            {{ session('status') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert-error-custom">
            <i class="fas fa-exclamation-circle fs-5"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Input Group -->
        <div class="input-group-custom">
            <label for="email" class="form-label">Alamat Email</label>
            <div style="position: relative">
                <input id="email" 
                       type="email" 
                       name="email" 
                       class="form-control-custom" 
                       placeholder="nama@email.com" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus>
                <i class="fas fa-envelope input-icon"></i>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit">
            Kirim Tautan Reset <i class="fas fa-paper-plane ms-1"></i>
        </button>

        <!-- Back to Login Link -->
        <div class="auth-switch-link">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left me-1"></i> Kembali ke Login</a>
        </div>
    </form>
</x-guest-layout>
