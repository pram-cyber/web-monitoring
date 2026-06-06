<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Smart Waste Monitor - {{ request()->routeIs('register') ? 'Daftar' : 'Login' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <!-- Bootstrap 5 CSS (Optional Helper) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS Variables and Premium Design Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        :root {
            --bg: radial-gradient(circle at 10% 20%, #f4f6f9 0%, #eef1f6 90%);
            --card-bg: rgba(255, 255, 255, 0.75);
            --text: #1e293b;
            --text-muted: #64748b;
            --border: rgba(226, 232, 240, 0.8);
            --input-bg: rgba(255, 255, 255, 0.9);
            --shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.06), 0 0 0 1px rgba(15, 23, 42, 0.05);
            --primary: #22c55e;
            --primary-glow: rgba(34, 197, 94, 0.3);
            --primary-hover: #16a34a;
            --orb-1: rgba(34, 197, 94, 0.15);
            --orb-2: rgba(37, 99, 235, 0.1);
        }

        [data-theme="dark"] {
            --bg: radial-gradient(circle at top left, #0b0b16, #06060c);
            --card-bg: rgba(15, 15, 28, 0.65);
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.05);
            --input-bg: rgba(10, 10, 18, 0.5);
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            --primary: #22c55e;
            --primary-glow: rgba(34, 197, 94, 0.4);
            --primary-hover: #16a34a;
            --orb-1: rgba(34, 197, 94, 0.25);
            --orb-2: rgba(37, 99, 235, 0.15);
        }

        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            transition: background 0.4s ease, color 0.4s ease;
        }

        /* ===== BACKGROUND GLOWING ORBS ===== */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            z-index: 1;
            pointer-events: none;
            animation: float 20s infinite alternate;
        }
        .orb-1 {
            width: 400px;
            height: 400px;
            background: var(--orb-1);
            top: -100px;
            left: -100px;
        }
        .orb-2 {
            width: 500px;
            height: 500px;
            background: var(--orb-2);
            bottom: -150px;
            right: -150px;
            animation-duration: 25s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(60px, 30px) scale(1.1); }
            100% { transform: translate(-30px, -60px) scale(0.9); }
        }

        /* ===== THEME TOGGLE BUTTON ===== */
        .theme-toggle-btn {
            position: absolute;
            top: 25px;
            right: 25px;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 999;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .theme-toggle-btn:hover {
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        /* ===== GLASS CONTAINER ===== */
        .auth-container {
            width: 100%;
            max-width: 460px;
            margin: 20px;
            padding: 40px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            z-index: 10;
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .auth-logo-box {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #22c55e, #15803d);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            margin-bottom: 16px;
            box-shadow: 0 10px 25px -5px rgba(34, 197, 94, 0.4);
            animation: pulseGlow 3s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 10px 25px -5px rgba(34, 197, 94, 0.4); }
            50% { box-shadow: 0 10px 30px 5px rgba(34, 197, 94, 0.6); }
        }

        .auth-title {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: var(--text);
            margin-bottom: 6px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ===== FORMS & INPUTS ===== */
        .form-label {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            display: block;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 22px;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            transition: color 0.3s ease;
            pointer-events: none;
            z-index: 5;
        }

        .form-control-custom {
            width: 100%;
            padding: 14px 16px 14px 46px;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 15px;
            font-weight: 500;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01);
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            background: var(--card-bg);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .form-control-custom:focus + .input-icon {
            color: var(--primary);
        }

        .remember-forgot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 28px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: var(--text-muted);
            font-weight: 500;
        }

        .checkbox-input {
            width: 17px;
            height: 17px;
            border-radius: 5px;
            border: 1px solid var(--border);
            outline: none;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px -8px rgba(34, 197, 94, 0.35);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -10px rgba(34, 197, 94, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* ===== Auth Switch Link ===== */
        .auth-switch-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .auth-switch-link a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .auth-switch-link a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* ===== Laravel Validation Alerts ===== */
        .alert-error-custom {
            background: rgba(239, 68, 68, 0.08);
            border: 1px dashed rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 550;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

    <!-- Glowing Background Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <!-- Theme Toggle Button -->
    <button class="theme-toggle-btn" onclick="toggleTheme()" id="theme-btn" title="Toggle Tema">
        <i class="fas fa-moon" id="theme-icon"></i>
    </button>

    <!-- Glass Auth Container -->
    <div class="auth-container">
        
        <!-- Logo & Header -->
        <div class="auth-header">
            <div class="auth-logo-box">
                <i class="fas fa-trash-alt"></i>
            </div>
            @if(request()->routeIs('register'))
                <h1 class="auth-title">Buat Akun Baru</h1>
                <p class="auth-subtitle">Bergabung dengan Smart Waste Management</p>
            @elseif(request()->routeIs('password.request'))
                <h1 class="auth-title">Lupa Kata Sandi</h1>
                <p class="auth-subtitle">Reset kata sandi akun Anda</p>
            @elseif(request()->routeIs('password.reset'))
                <h1 class="auth-title">Atur Ulang Sandi</h1>
                <p class="auth-subtitle">Buat kata sandi baru untuk akun Anda</p>
            @else
                <h1 class="auth-title">Selamat Datang Kembali</h1>
                <p class="auth-subtitle">Smart Waste Management System</p>
            @endif
        </div>

        {{ $slot }}

    </div>

    <!-- Theme Toggle Script -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            if (html.getAttribute('data-theme') === 'light') {
                html.setAttribute('data-theme', 'dark');
                icon.classList.replace('fa-moon', 'fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                html.setAttribute('data-theme', 'light');
                icon.classList.replace('fa-sun', 'fa-moon');
                localStorage.setItem('theme', 'light');
            }
        }

        // Apply saved theme on page load
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.getElementById('theme-icon').classList.replace('fa-moon', 'fa-sun');
        }
    </script>
</body>
</html>
