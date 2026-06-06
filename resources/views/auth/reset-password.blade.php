<x-guest-layout>
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

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Input Group -->
        <div class="input-group-custom">
            <label for="email" class="form-label">Alamat Email</label>
            <div style="position: relative">
                <input id="email" 
                       type="email" 
                       name="email" 
                       class="form-control-custom" 
                       placeholder="nama@email.com" 
                       value="{{ old('email', $request->email) }}" 
                       required 
                       autofocus 
                       autocomplete="username">
                <i class="fas fa-envelope input-icon"></i>
            </div>
        </div>

        <!-- New Password Input Group -->
        <div class="input-group-custom">
            <label for="password" class="form-label">Kata Sandi Baru</label>
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
                        onclick="togglePw('password', 'pw-eye')" 
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
                       placeholder="Ulangi kata sandi baru" 
                       required 
                       autocomplete="new-password">
                <i class="fas fa-shield-halved input-icon"></i>
                <button type="button" 
                        onclick="togglePw('password_confirmation', 'pw-c-eye')" 
                        style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; z-index: 10;">
                    <i class="fas fa-eye" id="pw-c-eye"></i>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit">
            Reset Kata Sandi <i class="fas fa-key ms-1"></i>
        </button>

        <!-- Back to Login Link -->
        <div class="auth-switch-link">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left me-1"></i> Kembali ke Login</a>
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
