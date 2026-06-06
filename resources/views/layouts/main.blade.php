<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Waste Monitor</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --sidebar-bg: rgba(26, 26, 46, 0.95);
            --sidebar-icon: rgba(255, 255, 255, 0.55);
            --sidebar-active: #ffffff;
            --bg: radial-gradient(circle at 10% 20%, #f4f6f9 0%, #eef1f6 90%);
            --card-bg: rgba(255, 255, 255, 0.72);
            --text: #1a1a3a;
            --text-muted: #6c758d;
            --border: rgba(233, 236, 239, 0.7);
            --topbar-bg: rgba(255, 255, 255, 0.72);
        }
        [data-theme="dark"] {
            --bg: radial-gradient(circle at top left, #0d0d1e, #07070f);
            --card-bg: rgba(26, 26, 46, 0.65);
            --text: #f8f9fa;
            --text-muted: #adb5bd;
            --border: rgba(45, 45, 78, 0.4);
            --topbar-bg: rgba(26, 26, 46, 0.65);
        }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* ===== SIDEBAR ===== */
        #sidebar {
            width: 60px;
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 0;
            gap: 5px;
        }
        .sidebar-brand {
            width: 40px;
            height: 40px;
            background: #22c55e;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .sidebar-item {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--sidebar-icon);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            position: relative;
        }
        .sidebar-item:hover,
        .sidebar-item.active {
            background: rgba(255,255,255,0.15);
            color: var(--sidebar-active);
        }
        .sidebar-item .tooltip-label {
            position: absolute;
            left: 55px;
            background: #333;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
            z-index: 9999;
        }
        .sidebar-item:hover .tooltip-label { opacity: 1; }
        .sidebar-bottom {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }
        .sidebar-avatar {
            width: 36px;
            height: 36px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
        }

        /* ===== MAIN ===== */
        #main {
            margin-left: 60px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== TOPBAR ===== */
        #topbar {
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--border);
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 99;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .topbar-title h5 {
            font-weight: 700;
            font-size: 18px;
            margin: 0;
        }
        .topbar-title small {
            color: var(--text-muted);
            font-size: 12px;
        }
        .live-badge {
            background: #dcfce7;
            color: #16a34a;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .live-dot {
            width: 8px;
            height: 8px;
            background: #16a34a;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        .btn-refresh {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-refresh:hover { background: #1d4ed8; }

        /* ===== CARDS ===== */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.02);
        }
        .stat-card .label {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
        }
        .stat-card .sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* ===== TABS ===== */
        .tab-nav {
            display: flex;
            gap: 0;
            border-bottom: 2px solid var(--border);
            margin-bottom: 0;
        }
        .tab-btn {
            padding: 12px 20px;
            border: none;
            background: none;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .tab-btn.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
        }
        .tab-content-area { display: none; }
        .tab-content-area.active { display: block; }

        /* ===== MAP ===== */
        #map {
            height: 480px;
            width: 100%;
            background: #f0f4ff;
            border-radius: 0 0 12px 12px;
        }

        /* ===== BIN CARD (Volume Panel) ===== */
        .bin-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 10px;
        }
        .bin-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 4px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .bin-info { flex: 1; }
        .bin-name { font-weight: 600; font-size: 14px; }
        .bin-loc { color: var(--text-muted); font-size: 12px; margin-bottom: 6px; }
        .bin-progress {
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 4px;
        }
        .bin-progress-bar { height: 100%; border-radius: 3px; }
        .bin-cap { font-size: 11px; color: var(--text-muted); display: flex; justify-content: space-between; }
        .bin-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            flex-shrink: 0;
        }

        /* ===== CONTENT ===== */
        #page-content { padding: 20px 24px; flex: 1; }

        /* ===== STATUS LEGEND ===== */
        .legend-box {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            z-index: 999;
        }

        /* Dark mode toggle */
        .dark-toggle {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            color: var(--text);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-custom {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.03);
        }
        
        /* ===== STYLING TABEL PREMIUM & DARK MODE ACCENT ===== */
        .table {
            --bs-table-bg: var(--card-bg) !important;
            --bs-table-color: var(--text) !important;
            --bs-table-border-color: var(--border) !important;
            color: var(--text) !important;
            background-color: var(--card-bg) !important;
        }
        .table > :not(caption) > * > * {
            background-color: var(--card-bg) !important;
            color: var(--text) !important;
            border-color: var(--border) !important;
            box-shadow: none !important;
        }
        .table th, .table td {
            border-color: var(--border) !important;
            background-color: var(--card-bg) !important;
            color: var(--text) !important;
        }
        .table thead th, .table > thead > tr > th {
            background-color: var(--bg) !important;
            color: var(--text) !important;
            font-weight: 700;
            border-bottom: 2px solid var(--border) !important;
        }
        .table-hover tbody tr:hover > * {
            background-color: rgba(34, 197, 94, 0.08) !important;
            color: var(--text) !important;
        }
        .text-muted {
            color: var(--text-muted) !important;
        }

        /* ===== CUSTOM PREMIUM LANGUAGE SELECTOR DROPDOWN ===== */
        .btn-lang {
            background: var(--card-bg);
            color: var(--text);
            border: 1px solid var(--border);
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }
        .btn-lang:hover, .btn-lang:focus, .btn-lang[aria-expanded="true"] {
            border-color: #2563eb;
            background: rgba(37, 99, 235, 0.04);
            color: #2563eb;
        }
        .btn-lang::after {
            display: none !important; /* Hide bootstrap default arrow */
        }
        .lang-menu {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            min-width: 170px;
            margin-top: 6px !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .lang-menu .dropdown-item {
            color: var(--text);
            padding: 8px 12px;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .lang-menu .dropdown-item:hover {
            background: rgba(37, 99, 235, 0.06);
            color: #2563eb;
        }
        
        /* Premium emblem badges to replace flag emojis */
        .flag-badge-premium {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb !important;
            border: 1px solid rgba(37, 99, 235, 0.2);
            text-transform: uppercase;
            flex-shrink: 0;
            line-height: 1;
        }
        [data-theme="dark"] .flag-badge-premium {
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e !important;
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        /* Hide google translate banner frame completely */
        .skiptranslate {
            display: none !important;
        }
        body {
            top: 0px !important;
        }

        /* ===== MODERN PREMIUM EMPTY STATE ===== */
        .empty-state {
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 12px;
            background: rgba(37, 99, 235, 0.02);
            border: 1px dashed var(--border);
            transition: all 0.3s ease;
            margin: 15px 0;
        }
        .empty-state i {
            font-size: 40px;
            background: linear-gradient(135deg, #2563eb, #22c55e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.8;
            margin-bottom: 5px;
            animation: floatEmpty 3s ease-in-out infinite;
        }
        .empty-state span {
            font-size: 15px;
            font-weight: 600;
            color: var(--text) !important;
            opacity: 0.9;
        }
        .empty-state p {
            font-size: 12px;
            color: var(--text-muted) !important;
            margin: 0;
            max-width: 280px;
            text-align: center;
            line-height: 1.5;
        }
        @keyframes floatEmpty {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* ===== MAIN LAYOUT FLOATING NEON ORBS ===== */
        .main-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(140px);
            z-index: -1;
            pointer-events: none;
            opacity: 0.55;
            transition: all 0.5s ease;
        }
        .main-orb-1 {
            width: 350px;
            height: 350px;
            background: rgba(34, 197, 94, 0.15); /* Neon Green glow */
            top: 10%;
            right: 5%;
        }
        .main-orb-2 {
            width: 450px;
            height: 450px;
            background: rgba(37, 99, 235, 0.08); /* Blue glow */
            bottom: 5%;
            left: 10%;
        }
        [data-theme="dark"] .main-orb-1 {
            background: rgba(34, 197, 94, 0.22);
            opacity: 0.7;
        }
        [data-theme="dark"] .main-orb-2 {
            background: rgba(37, 99, 235, 0.15);
            opacity: 0.7;
        }

        /* ===== SIDEBAR BADGE NOTIFICATION PREMIUM ===== */
        .sidebar-badge-premium {
            position: absolute;
            top: 4px;
            right: 4px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white !important;
            font-size: 9px;
            font-weight: 800;
            width: 17px;
            height: 17px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1.5px solid var(--sidebar-bg);
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.45);
            animation: badgePulse 2s infinite;
            pointer-events: none;
            z-index: 5;
            line-height: 1;
        }
        @keyframes badgePulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(239, 68, 68, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        /* ===== LEAFLET MAP DARK MODE HARMONIZATION ===== */
        [data-theme="dark"] .leaflet-tile {
            filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%);
        }
        [data-theme="dark"] .leaflet-container {
            background: #111122 !important;
        }
        [data-theme="dark"] .leaflet-bar a {
            background-color: var(--card-bg) !important;
            color: var(--text) !important;
            border-color: var(--border) !important;
        }
        [data-theme="dark"] .leaflet-popup-content-wrapper,
        [data-theme="dark"] .leaflet-popup-tip {
            background: var(--card-bg) !important;
            color: var(--text) !important;
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Background Ambient Glowing Orbs -->
<div class="main-orb main-orb-1"></div>
<div class="main-orb main-orb-2"></div>

<!-- SIDEBAR -->
<div id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-recycle"></i>
    </div>    @if(auth()->user()->role === 'admin')
    <a href="{{ route('admin.dashboard') }}"
       class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt fa-lg"></i>
        <span class="tooltip-label">Dashboard</span>
    </a>
    <a href="{{ route('admin.history') }}"
       class="sidebar-item {{ request()->routeIs('admin.history') ? 'active' : '' }}">
        <i class="fas fa-history fa-lg"></i>
        <span class="tooltip-label">Riwayat</span>
    </a>
    <a href="{{ route('admin.sensor-logs') }}"
       class="sidebar-item {{ request()->routeIs('admin.sensor-logs') ? 'active' : '' }}">
        <i class="fas fa-microchip fa-lg"></i>
        <span class="tooltip-label">Sensor Logs</span>
    </a>
    <a href="{{ route('admin.users') }}"
       class="sidebar-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
        <i class="fas fa-users fa-lg"></i>
        <span class="tooltip-label">Manajemen User</span>
    </a>
    <a href="{{ route('admin.reports') }}"
       class="sidebar-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}"
       style="position: relative;">
        <i class="fas fa-flag fa-lg"></i>
        @php
            $pendingReportsCount = \App\Models\Report::where('status', 'pending')->count();
        @endphp
        <span id="sidebar-reports-badge" class="sidebar-badge-premium" style="display: {{ $pendingReportsCount > 0 ? 'flex' : 'none' }}">
            {{ $pendingReportsCount }}
        </span>
        <span class="tooltip-label">Laporan User</span>
    </a>
    @else
    <a href="{{ route('user.dashboard') }}" 
       class="sidebar-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
        <i class="fas fa-chart-bar fa-lg"></i>
        <span class="tooltip-label">Dashboard</span>
    </a>
    <a href="{{ route('user.nearby') }}" 
       class="sidebar-item {{ request()->routeIs('user.nearby') ? 'active' : '' }}">
        <i class="fas fa-map-marker-alt fa-lg"></i>
        <span class="tooltip-label">Tong Terdekat</span>
    </a>
    <a href="{{ route('user.history') }}" 
       class="sidebar-item {{ request()->routeIs('user.history') ? 'active' : '' }}">
        <i class="fas fa-history fa-lg"></i>
        <span class="tooltip-label">Riwayat</span>
    </a>
    <a href="{{ route('user.reports') }}" 
       class="sidebar-item {{ request()->routeIs('user.reports') ? 'active' : '' }}">
        <i class="fas fa-flag fa-lg"></i>
        <span class="tooltip-label">Laporan</span>
    </a>
    @endif

    <div class="sidebar-bottom">
        <button class="dark-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
            <i class="fas fa-moon" id="dark-icon"></i>
        </button>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
            @csrf
        </form>
        <button type="button" onclick="confirmLogout(event)" class="sidebar-item" title="Logout" style="background:none;border:none;">
            <i class="fas fa-sign-out-alt fa-lg" style="color:rgba(255,255,255,0.5)"></i>
            <span class="tooltip-label">Logout</span>
        </button>
        <div onclick="window.location.href='{{ route('settings') }}'" class="sidebar-avatar text-decoration-none" 
             title="Pengaturan Akun & Hapus Akun" 
             style="transition: all 0.2s ease; text-decoration: none; border: {{ request()->routeIs('settings') ? '2px solid #ffffff' : 'none' }}; box-shadow: {{ request()->routeIs('settings') ? '0 0 12px rgba(255,255,255,0.4)' : 'none' }}; cursor: pointer;"
             onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 0 12px rgba(34, 197, 94, 0.6)';"
             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='{{ request()->routeIs('settings') ? '0 0 12px rgba(255,255,255,0.4)' : 'none' }}';">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- MAIN -->
<div id="main">
    <!-- TOPBAR -->
    <div id="topbar">
        <div class="topbar-title">
            <h5>Smart Waste Monitor</h5>
            <small>Real-time GPS tracking • Update: <span id="update-time"></span></small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Hidden Google Translate Element -->
            <div id="google_translate_element" style="display:none !important"></div>
            
            <!-- Custom Premium Language Selector Dropdown -->
            <div class="dropdown">
                <button class="btn-lang dropdown-toggle" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-globe"></i>
                    <span id="current-lang-label"><span class="flag-badge-premium me-1">ID</span> Indonesia</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end lang-menu" aria-labelledby="langDropdown">
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('id')"><span class="flag-badge-premium">ID</span> Indonesia</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('en')"><span class="flag-badge-premium">EN</span> English</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('ar')"><span class="flag-badge-premium">AR</span> العربية</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('ja')"><span class="flag-badge-premium">JA</span> 日本語</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeLanguage('zh-CN')"><span class="flag-badge-premium">ZH</span> 简体中文</a></li>
                </ul>
            </div>

            <button class="btn-refresh" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </button>
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div id="page-content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Update time
    function updateTime() {
        const now = new Date();
        document.getElementById('update-time').textContent =
            now.getHours().toString().padStart(2,'0') + '.' +
            now.getMinutes().toString().padStart(2,'0') + '.' +
            now.getSeconds().toString().padStart(2,'0');
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Dark mode
    function toggleDarkMode() {
        const html = document.documentElement;
        const icon = document.getElementById('dark-icon');
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
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.getElementById('dark-icon')?.classList.replace('fa-moon', 'fa-sun');
    }

    // Refresh
    function refreshData() {
        const icon = document.querySelector('.btn-refresh i');
        if (icon) {
            icon.classList.add('fa-spin');
            setTimeout(() => icon.classList.remove('fa-spin'), 1000);
        }
        if (typeof triggerAutoRefresh === 'function') {
            triggerAutoRefresh();
        } else {
            location.reload();
        }
    }

    // Tabs
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content-area').forEach(c => c.classList.remove('active'));
        document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
        document.getElementById(`tab-${tab}`).classList.add('active');
    }

    // Global SweetAlert2 helper to replace native confirm() safely
    window.confirmAction = function(event, message) {
        event.preventDefault(); // Stop native submission
        const form = event.currentTarget || event.target;
        const isDelete = message.toLowerCase().includes('hapus') || message.toLowerCase().includes('clear') || message.toLowerCase().includes('delete');
        
        Swal.fire({
            title: isDelete ? 'Konfirmasi Hapus' : 'Konfirmasi Tindakan',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isDelete ? '#dc2626' : '#2563eb',
            cancelButtonColor: '#6c757d',
            confirmButtonText: isDelete ? 'Ya, Hapus!' : 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
            color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e',
            borderRadius: '16px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Temporarily remove the onsubmit to avoid recursion
                const originalOnsubmit = form.getAttribute('onsubmit');
                form.removeAttribute('onsubmit');
                form.submit();
                form.setAttribute('onsubmit', originalOnsubmit);
            }
        });
        return false;
    }

    // Global SweetAlert2 Form Pre-processor using Event Delegation to Intercept Native Confirm (works on dynamically rendered elements)
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const onsubmitAttr = form.getAttribute('onsubmit');
        if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
            e.preventDefault(); // Stop standard submit
            
            // Extract the confirm message
            const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
            const message = match ? match[1] : 'Apakah Anda yakin?';
            
            Swal.fire({
                title: 'Konfirmasi Tindakan',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
                color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e',
                borderRadius: '12px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Temporarily bypass the onsubmit intercept to perform clean submission
                    const originalOnsubmit = form.getAttribute('onsubmit');
                    form.removeAttribute('onsubmit');
                    form.submit();
                    form.setAttribute('onsubmit', originalOnsubmit);
                }
            });
        }
    });

    // Global Toast Notification for Laravel Session Success
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
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
                background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
                color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e'
            });
        });
    @endif

    // SweetAlert2 Premium Logout Confirmation
    function confirmLogout(e) {
        if (e) e.preventDefault();
        
        Swal.fire({
            title: 'Konfirmasi Keluar',
            text: 'Apakah Anda yakin ingin keluar dari sesi Smart Waste Monitor? Anda harus masuk kembali untuk mengelola atau melihat data monitoring.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal',
            background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
            color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e',
            borderRadius: '16px',
        }).then((result) => {
            if (result.isConfirmed) {
                // Show a loading/processing alert before redirecting
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mengakhiri sesi Anda dengan aman.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
                    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e',
                });
                
                setTimeout(() => {
                    document.getElementById('logout-form').submit();
                }, 800);
            }
        });
    }
</script>
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'id'
        }, 'google_translate_element');
    }

    // Function to programmatically switch languages via our premium dropdown
    function changeLanguage(langCode) {
        const selectEl = document.querySelector('.goog-te-combo');
        if (selectEl) {
            selectEl.value = langCode;
            selectEl.dispatchEvent(new Event('change'));
            updateDropdownLabel(langCode);
            localStorage.setItem('custom-lang', langCode);
        } else {
            console.warn("Google Translate combo box not generated yet.");
        }
    }

    // Helper to update the text label of the dropdown button
    function updateDropdownLabel(langCode) {
        const langLabels = {
            'id': '<span class="flag-badge-premium me-1">ID</span> Indonesia',
            'en': '<span class="flag-badge-premium me-1">EN</span> English',
            'ar': '<span class="flag-badge-premium me-1">AR</span> العربية',
            'ja': '<span class="flag-badge-premium me-1">JA</span> 日本語',
            'zh-CN': '<span class="flag-badge-premium me-1">ZH</span> 简体中文'
        };
        const labelEl = document.getElementById('current-lang-label');
        if (labelEl) {
            labelEl.innerHTML = langLabels[langCode] || 'Bahasa';
        }
    }

    // Polling script to apply saved language selection when Google element finishes loading
    function checkGoogleTranslateReady() {
        const selectEl = document.querySelector('.goog-te-combo');
        if (selectEl) {
            const savedLang = localStorage.getItem('custom-lang') || 'id';
            if (savedLang !== 'id') {
                setTimeout(() => {
                    changeLanguage(savedLang);
                }, 600);
            } else {
                updateDropdownLabel('id');
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