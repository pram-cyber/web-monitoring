<x-guest-layout>

    <!-- Validation Errors Alert Box -->
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

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name Input Group -->
        <div class="input-group-custom">
            <label for="name" class="form-label">Nama Lengkap</label>
            <div style="position: relative">
                <input id="name" 
                       type="text" 
                       name="name" 
                       class="form-control-custom" 
                       placeholder="Masukkan nama lengkap" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       autocomplete="name">
                <i class="fas fa-user input-icon"></i>
            </div>
        </div>

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
                       autocomplete="username">
                <i class="fas fa-envelope input-icon"></i>
            </div>
        </div>

        <!-- Password Input Group -->
        <div class="input-group-custom">
            <label for="password" class="form-label">Kata Sandi</label>
            <div style="position: relative">
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="form-control-custom" 
                       placeholder="Minimal 8 karakter" 
                       required 
                       autocomplete="new-password">
                <i class="fas fa-lock input-icon"></i>
                <button type="button" 
                        onclick="togglePassword('password', 'pw-eye')" 
                        style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; z-index: 10;">
                    <i class="fas fa-eye" id="pw-eye"></i>
                </button>
            </div>
        </div>

        <!-- Confirm Password Input Group -->
        <div class="input-group-custom">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <div style="position: relative">
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       class="form-control-custom" 
                       placeholder="Ulangi kata sandi" 
                       required 
                       autocomplete="new-password">
                <i class="fas fa-shield-halved input-icon"></i>
                <button type="button" 
                        onclick="togglePassword('password_confirmation', 'pw-confirm-eye')" 
                        style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; z-index: 10;">
                    <i class="fas fa-eye" id="pw-confirm-eye"></i>
                </button>
            </div>
        </div>

        <!-- Register Submit Button -->
        <button type="submit" class="btn-submit">
            Buat Akun <i class="fas fa-user-plus ms-1"></i>
        </button>

        <!-- Login Link -->
        <div class="auth-switch-link">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </form>

    <script>
        function togglePassword(inputId, iconId) {
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
