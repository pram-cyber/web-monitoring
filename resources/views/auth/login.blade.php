<x-guest-layout>
    <!-- Session Status (e.g. password resets success) -->
    @if (session('status'))
        <div class="alert alert-success mb-4" style="border-radius: 12px; font-weight: 550; font-size: 14px">
            {{ session('status') }}
        </div>
    @endif

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

    <form method="POST" action="{{ route('login') }}">
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
                       autofocus 
                       autocomplete="username">
                <i class="fas fa-envelope input-icon"></i>
            </div>
        </div>

        <!-- Password Input Group -->
        <div class="input-group-custom">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0">Kata Sandi</label>
            </div>
            <div style="position: relative">
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="form-control-custom" 
                       placeholder="••••••••" 
                       required 
                       autocomplete="current-password">
                <i class="fas fa-lock input-icon"></i>
                <button type="button" 
                        onclick="togglePasswordVisibility()" 
                        style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; z-index: 10;">
                    <i class="fas fa-eye" id="password-eye-icon"></i>
                </button>
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="remember-forgot">
            <label for="remember_me" class="checkbox-container">
                <input id="remember_me" type="checkbox" class="checkbox-input" name="remember">
                <span>Ingat saya</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif
        </div>

        <!-- Login Submit Button -->
        <button type="submit" class="btn-submit">
            Masuk ke Dashboard <i class="fas fa-arrow-right ms-1"></i>
        </button>

        <!-- Register Link -->
        <div class="auth-switch-link">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
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
