<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Waste Monitor</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
 
    <!-- MapLibre GL & FontAwesome -->
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@5.24.0/dist/maplibre-gl.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Hide MapLibre Watermark / Attribution & Logo */
        .maplibregl-ctrl-attrib, .maplibregl-ctrl-logo {
            display: none !important;
        }
        
        /* Custom MapLibre Popups Dark Mode Harmonization */
        .dark .maplibregl-popup-content {
            background: rgb(15 23 42 / 0.85) !important;
            color: #f8fafc !important;
            border: 1px solid rgb(30 41 59 / 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        .dark .maplibregl-popup-anchor-top .maplibregl-popup-tip {
            border-bottom-color: rgb(15 23 42 / 0.85) !important;
        }
        .dark .maplibregl-popup-anchor-bottom .maplibregl-popup-tip {
            border-top-color: rgb(15 23 42 / 0.85) !important;
        }
        .dark .maplibregl-popup-anchor-left .maplibregl-popup-tip {
            border-right-color: rgb(15 23 42 / 0.85) !important;
        }
        .dark .maplibregl-popup-anchor-right .maplibregl-popup-tip {
            border-left-color: rgb(15 23 42 / 0.85) !important;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased font-sans transition-colors duration-300">

<!-- Background Ambient Glowing Orbs -->
<div class="fixed top-1/4 right-10 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/15 rounded-full blur-[140px] pointer-events-none -z-10 animate-pulse"></div>
<div class="fixed bottom-10 left-10 w-96 h-96 bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-[140px] pointer-events-none -z-10 animate-pulse"></div>

<!-- SIDEBAR -->
<div id="sidebar" class="w-16 h-screen bg-slate-900 dark:bg-slate-950 border-r border-slate-800 dark:border-slate-900/50 flex flex-col items-center py-5 gap-1.5 fixed left-0 top-0 z-50">
    <!-- Brand Logo -->
    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white text-lg mb-6 shadow-lg shadow-emerald-500/20">
        <i class="fas fa-recycle animate-spin-slow"></i>
    </div>

    <!-- Navigation Items -->
    @if(auth()->user()->role === 'admin')
    <a href="{{ route('admin.dashboard') }}"
       class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? '!text-white bg-slate-800/80' : '' }}">
        <i class="fas fa-tachometer-alt text-lg"></i>
        <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Dashboard</span>
    </a>
    <a href="{{ route('admin.history') }}"
       class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 {{ request()->routeIs('admin.history') ? '!text-white bg-slate-800/80' : '' }}">
        <i class="fas fa-history text-lg"></i>
        <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Riwayat</span>
    </a>
    <a href="{{ route('admin.sensor-logs') }}"
       class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 {{ request()->routeIs('admin.sensor-logs') ? '!text-white bg-slate-800/80' : '' }}">
        <i class="fas fa-microchip text-lg"></i>
        <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Sensor Logs</span>
    </a>
    <a href="{{ route('admin.users') }}"
       class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 {{ request()->routeIs('admin.users') ? '!text-white bg-slate-800/80' : '' }}">
        <i class="fas fa-users text-lg"></i>
        <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Manajemen User</span>
    </a>
    <a href="{{ route('admin.reports') }}"
       class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 {{ request()->routeIs('admin.reports') ? '!text-white bg-slate-800/80' : '' }}">
        <i class="fas fa-flag text-lg"></i>
        @php
            $pendingReportsCount = \App\Models\Report::where('status', 'pending')->count();
        @endphp
        <span id="sidebar-reports-badge" class="absolute top-1 right-1 bg-gradient-to-br from-red-500 to-rose-600 text-white text-[9px] font-extrabold w-4.5 h-4.5 rounded-full flex items-center justify-center border border-slate-900 shadow-md shadow-red-500/35 animate-bounce" style="display: {{ $pendingReportsCount > 0 ? 'flex' : 'none' }}">
            {{ $pendingReportsCount }}
        </span>
        <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Laporan User</span>
    </a>
    @else
    <a href="{{ route('user.dashboard') }}" 
       class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 {{ request()->routeIs('user.dashboard') ? '!text-white bg-slate-800/80' : '' }}">
        <i class="fas fa-chart-bar text-lg"></i>
        <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Dashboard</span>
    </a>
    <a href="{{ route('user.nearby') }}" 
       class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 {{ request()->routeIs('user.nearby') ? '!text-white bg-slate-800/80' : '' }}">
        <i class="fas fa-map-marker-alt text-lg"></i>
        <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Tong Terdekat</span>
    </a>
    <a href="{{ route('user.history') }}" 
       class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 {{ request()->routeIs('user.history') ? '!text-white bg-slate-800/80' : '' }}">
        <i class="fas fa-history text-lg"></i>
        <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Riwayat</span>
    </a>
    <a href="{{ route('user.reports') }}" 
       class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 {{ request()->routeIs('user.reports') ? '!text-white bg-slate-800/80' : '' }}">
        <i class="fas fa-flag text-lg"></i>
        <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Laporan</span>
    </a>
    @endif

    <!-- Sidebar Bottom Actions -->
    <div class="mt-auto flex flex-col items-center gap-2">
        <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-slate-800/40 text-slate-400 hover:text-white hover:bg-slate-800/80 border border-slate-800/50 flex items-center justify-center cursor-pointer transition-all duration-200" title="Toggle Dark Mode">
            <i class="fas fa-moon" id="dark-icon"></i>
        </button>
        
        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
        <button onclick="confirmLogout(event)" class="group relative w-11 h-11 rounded-xl flex items-center justify-center text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 transition-all duration-200 cursor-pointer">
            <i class="fas fa-sign-out-alt text-lg"></i>
            <span class="absolute left-16 bg-slate-950 text-white text-xs px-2.5 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-lg border border-slate-800">Logout</span>
        </button>
        
        <div onclick="window.location.href='{{ route('settings') }}'" 
             class="w-9 h-9 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-sm flex items-center justify-center cursor-pointer transition-all duration-200 select-none {{ request()->routeIs('settings') ? 'ring-2 ring-white shadow-lg' : '' }}" 
             title="Pengaturan Akun & Hapus Akun">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- MAIN CONTAINER -->
<div id="main" class="pl-16 min-h-screen flex flex-col transition-all duration-300">
    <!-- TOPBAR -->
    <div id="topbar" class="sticky top-0 bg-white/70 dark:bg-slate-950/70 border-b border-slate-200/40 dark:border-slate-800/40 px-6 py-3.5 flex justify-between items-center backdrop-blur-xl z-40">
        <div class="space-y-0.5">
            <h5 class="font-bold text-base tracking-tight">Smart Waste Monitor</h5>
            <p class="text-xs text-slate-500 dark:text-slate-400">Real-time GPS tracking • Update: <span id="update-time" class="font-medium"></span></p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Hidden Google Translate Element -->
            <div id="google_translate_element" class="hidden"></div>
            
            <!-- Language Dropdown -->
            <div class="relative inline-block text-left" id="lang-dropdown-wrapper">
                <button onclick="toggleLangDropdown()" class="inline-flex items-center gap-2 px-3 py-2 bg-white/80 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl text-xs sm:text-sm font-semibold hover:border-blue-500 dark:hover:border-emerald-500 cursor-pointer shadow-sm transition-all focus:outline-none" id="langDropdownBtn">
                    <i class="fas fa-globe text-slate-400 dark:text-slate-500"></i>
                    <span id="current-lang-label">
                        <span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10 me-1">ID</span> Indonesia
                    </span>
                </button>
                <div class="hidden absolute right-0 mt-2 w-44 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl backdrop-blur-md p-1.5 focus:outline-none z-50" id="langDropdownMenu">
                    <a href="javascript:void(0)" onclick="selectLanguage('id')" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all">
                        <span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10">ID</span> Indonesia
                    </a>
                    <a href="javascript:void(0)" onclick="selectLanguage('en')" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all">
                        <span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10">EN</span> English
                    </a>
                    <a href="javascript:void(0)" onclick="selectLanguage('ar')" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all">
                        <span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10">AR</span> العربية
                    </a>
                    <a href="javascript:void(0)" onclick="selectLanguage('ja')" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all">
                        <span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10">JA</span> 日本語
                    </a>
                    <a href="javascript:void(0)" onclick="selectLanguage('zh-CN')" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all">
                        <span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10">ZH</span> 简体中文
                    </a>
                </div>
            </div>

            <!-- Refresh Button -->
            <button onclick="refreshData()" class="px-4 py-2 bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2 cursor-pointer shadow-md transition-all active:scale-95">
                <i class="fas fa-sync-alt" id="refresh-icon"></i> Refresh Data
            </button>
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div id="page-content" class="p-6 flex-grow flex flex-col">
        @yield('content')
    </div>
</div>

<!-- JavaScript Dependencies (No Bootstrap JS, styled libraries only) -->
<script src="https://unpkg.com/maplibre-gl@5.24.0/dist/maplibre-gl.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Update live clock
    function updateClock() {
        const now = new Date();
        const updateTimeEl = document.getElementById('update-time');
        if (updateTimeEl) {
            updateTimeEl.textContent =
                now.getHours().toString().padStart(2,'0') + ':' +
                now.getMinutes().toString().padStart(2,'0') + ':' +
                now.getSeconds().toString().padStart(2,'0');
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Light / Dark Theme toggle logic
    function toggleDarkMode() {
        const html = document.documentElement;
        const icon = document.getElementById('dark-icon');
        let newTheme = 'light';
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            icon?.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('theme', 'light');
            newTheme = 'light';
        } else {
            html.classList.add('dark');
            icon?.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('theme', 'dark');
            newTheme = 'dark';
        }
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
    }

    // Set initial theme
    const darkIcon = document.getElementById('dark-icon');
    if (localStorage.getItem('theme') === 'dark' || 
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        darkIcon?.classList.replace('fa-moon', 'fa-sun');
    } else {
        document.documentElement.classList.remove('dark');
        darkIcon?.classList.replace('fa-sun', 'fa-moon');
    }

    // Language Dropdown toggler
    function toggleLangDropdown() {
        const menu = document.getElementById('langDropdownMenu');
        menu?.classList.toggle('hidden');
    }
    
    // Close language menu on clicking outside
    document.addEventListener('click', function(e) {
        const btn = document.getElementById('langDropdownBtn');
        const menu = document.getElementById('langDropdownMenu');
        if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    // Refresh Data
    function refreshData() {
        const icon = document.getElementById('refresh-icon');
        if (icon) {
            icon.classList.add('animate-spin');
            setTimeout(() => icon.classList.remove('animate-spin'), 1000);
        }
        if (typeof triggerAutoRefresh === 'function') {
            triggerAutoRefresh();
        } else {
            location.reload();
        }
    }

    // Custom Switch Tab Helper (For Leaflet & List view togglers)
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active', 'border-blue-500', 'text-blue-600', 'dark:border-emerald-500', 'dark:text-emerald-400'));
        document.querySelectorAll('.tab-content-area').forEach(c => c.classList.add('hidden'));
        
        const activeBtn = document.querySelector(`[data-tab="${tab}"]`);
        activeBtn?.classList.add('active', 'border-blue-500', 'text-blue-600', 'dark:border-emerald-500', 'dark:text-emerald-400');
        document.getElementById(`tab-${tab}`)?.classList.remove('hidden');
    }

    // SweetAlert2 logout and confirm helpers
    window.confirmAction = function(event, message) {
        event.preventDefault();
        const form = event.currentTarget || event.target;
        const isDelete = message.toLowerCase().includes('hapus') || message.toLowerCase().includes('clear') || message.toLowerCase().includes('delete');
        const isDark = document.documentElement.classList.contains('dark');
        
        Swal.fire({
            title: isDelete ? 'Konfirmasi Hapus' : 'Konfirmasi Tindakan',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isDelete ? '#ef4444' : '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: isDelete ? 'Ya, Hapus!' : 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            background: isDark ? '#0f172a' : '#ffffff',
            color: isDark ? '#f8fafc' : '#1e293b',
            borderRadius: '16px'
        }).then((result) => {
            if (result.isConfirmed) {
                const originalOnsubmit = form.getAttribute('onsubmit');
                form.removeAttribute('onsubmit');
                form.submit();
                form.setAttribute('onsubmit', originalOnsubmit);
            }
        });
        return false;
    }

    // Intercept default confirm submits automatically
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const onsubmitAttr = form.getAttribute('onsubmit');
        if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
            e.preventDefault();
            const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
            const message = match ? match[1] : 'Apakah Anda yakin?';
            const isDark = document.documentElement.classList.contains('dark');
            
            Swal.fire({
                title: 'Konfirmasi Tindakan',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                background: isDark ? '#0f172a' : '#ffffff',
                color: isDark ? '#f8fafc' : '#1e293b',
                borderRadius: '16px'
            }).then((result) => {
                if (result.isConfirmed) {
                    const originalOnsubmit = form.getAttribute('onsubmit');
                    form.removeAttribute('onsubmit');
                    form.submit();
                    form.setAttribute('onsubmit', originalOnsubmit);
                }
            });
        }
    });

    // Global Toast notifications
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({
                icon: 'success',
                title: '{{ session("success") }}',
                background: isDark ? '#0f172a' : '#ffffff',
                color: isDark ? '#f8fafc' : '#1e293b'
            });
        });
    @endif

    // SweetAlert2 Confirm Logout
    function confirmLogout(e) {
        if (e) e.preventDefault();
        const isDark = document.documentElement.classList.contains('dark');
        
        Swal.fire({
            title: 'Konfirmasi Keluar',
            text: 'Apakah Anda yakin ingin keluar dari sesi Smart Waste Monitor?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal',
            background: isDark ? '#0f172a' : '#ffffff',
            color: isDark ? '#f8fafc' : '#1e293b',
            borderRadius: '16px',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mengakhiri sesi Anda dengan aman.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: isDark ? '#0f172a' : '#ffffff',
                    color: isDark ? '#f8fafc' : '#1e293b',
                });
                
                setTimeout(() => {
                    document.getElementById('logout-form').submit();
                }, 800);
            }
        });
    }
</script>

<!-- Google Translate Integration Script (Optimized) -->
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'id'
        }, 'google_translate_element');
    }

    function selectLanguage(langCode) {
        const selectEl = document.querySelector('.goog-te-combo');
        if (selectEl) {
            selectEl.value = langCode;
            selectEl.dispatchEvent(new Event('change'));
            updateLangDropdownLabel(langCode);
            localStorage.setItem('custom-lang', langCode);
            document.getElementById('langDropdownMenu')?.classList.add('hidden');
        }
    }

    function updateLangDropdownLabel(langCode) {
        const langLabels = {
            'id': '<span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10 me-1">ID</span> Indonesia',
            'en': '<span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10 me-1">EN</span> English',
            'ar': '<span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10 me-1">AR</span> العربية',
            'ja': '<span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10 me-1">JA</span> 日本語',
            'zh-CN': '<span class="inline-flex items-center justify-center text-[9px] font-extrabold w-5 h-5 rounded-md bg-blue-500/10 dark:bg-emerald-500/10 text-blue-600 dark:text-emerald-400 border border-blue-500/10 dark:border-emerald-500/10 me-1">ZH</span> 中文'
        };
        const labelEl = document.getElementById('current-lang-label');
        if (labelEl) {
            labelEl.innerHTML = langLabels[langCode] || 'Language';
        }
    }

    function checkGoogleTranslateReady() {
        const selectEl = document.querySelector('.goog-te-combo');
        if (selectEl) {
            const savedLang = localStorage.getItem('custom-lang') || 'id';
            if (savedLang !== 'id') {
                setTimeout(() => {
                    selectLanguage(savedLang);
                }, 600);
            } else {
                updateLangDropdownLabel('id');
            }
        } else {
            setTimeout(checkGoogleTranslateReady, 100);
        }
    }
    document.addEventListener('DOMContentLoaded', checkGoogleTranslateReady);
</script>
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
@stack('scripts')
</body>
</html>