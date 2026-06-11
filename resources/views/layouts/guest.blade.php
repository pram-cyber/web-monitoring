<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased transition-colors duration-300 flex items-center justify-center relative overflow-hidden">

    <!-- Glowing Background Orbs -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-full blur-[100px] pointer-events-none animate-[pulse_6s_infinite_alternate]"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-500/10 dark:bg-blue-500/15 rounded-full blur-[100px] pointer-events-none animate-[pulse_8s_infinite_alternate]"></div>

    <!-- Theme Toggle Button -->
    <button onclick="toggleTheme()" class="absolute top-6 right-6 w-11 h-11 rounded-xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/50 text-slate-800 dark:text-slate-100 flex items-center justify-center cursor-pointer transition-all duration-300 hover:scale-105 hover:-translate-y-0.5 shadow-sm hover:shadow-md z-50 backdrop-blur-md" title="Toggle Tema">
        <i class="fas fa-moon dark:hidden" id="theme-icon-light"></i>
        <i class="fas fa-sun hidden dark:block" id="theme-icon-dark"></i>
    </button>

    <!-- Glass Auth Container -->
    <div class="w-full max-w-[460px] m-5 p-8 sm:p-10 bg-white/80 dark:bg-slate-900/70 border border-slate-200/50 dark:border-slate-800/50 rounded-3xl shadow-xl dark:shadow-2xl shadow-slate-200/50 dark:shadow-black/30 backdrop-blur-xl z-10 opacity-0 animate-[slideUp_0.5s_cubic-bezier(0.16,1,0.3,1)_forwards]">
        
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl inline-flex items-center justify-center text-white text-3xl mb-4 shadow-lg shadow-emerald-500/35 animate-pulse">
                <i class="fas fa-trash-alt"></i>
            </div>
            @if(request()->routeIs('register'))
                <h1 class="text-2xl font-bold tracking-tight mb-1">Buat Akun Baru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Bergabung dengan Smart Waste Management</p>
            @elseif(request()->routeIs('password.request'))
                <h1 class="text-2xl font-bold tracking-tight mb-1">Lupa Kata Sandi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Reset kata sandi akun Anda</p>
            @elseif(request()->routeIs('password.reset'))
                <h1 class="text-2xl font-bold tracking-tight mb-1">Atur Ulang Sandi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Buat kata sandi baru untuk akun Anda</p>
            @else
                <h1 class="text-2xl font-bold tracking-tight mb-1">Selamat Datang Kembali</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Smart Waste Management System</p>
            @endif
        </div>

        {{ $slot }}

    </div>

    <!-- Custom Animation Styles and JS Theme Toggle -->
    <style>
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
    
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Apply theme on load
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
