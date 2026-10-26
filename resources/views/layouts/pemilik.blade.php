<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Pemilik Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8faf8; }
        .sidebar { width: 260px; height: 100vh; overflow-y: auto; background: #243642; color: #fff; position: fixed; top: 0; left: 0; z-index: 1000; transition: .3s; }
        .sidebar .nav-link { color: #adb5bd; font-weight: 500; padding: 12px 20px; border-radius: 10px; margin: 4px 15px; transition: .3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: #387478; }
        .main-content { margin-left: 260px; padding: 30px; min-height: 100vh; }
        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
        }
        @media(max-width: 991px) {
            .sidebar { margin-left: -260px; }
            .sidebar.show { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-0" id="pemilikSidebar">
    <div class="p-4 d-flex align-items-center border-bottom border-secondary">
        <img src="{{ asset('images/logo1.jpg') }}" width="40" height="40" class="rounded-circle me-2">
        <h5 class="fw-bold mb-0 text-white fs-6">Pemilik Panel</h5>
    </div>
    <ul class="nav nav-pills flex-column mt-3 mb-auto">
        <li class="nav-item">
            <a href="{{ route('pemilik.dashboard') }}" class="nav-link {{ request()->routeIs('pemilik.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="#menuLaporanPemilik" class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('pemilik.laporan.*') ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('pemilik.laporan.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan</span>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <div class="collapse {{ request()->routeIs('pemilik.laporan.*') ? 'show' : '' }} ps-3" id="menuLaporanPemilik">
                <ul class="nav flex-column mt-1">
                    <li class="nav-item">
                        <a href="{{ route('pemilik.laporan.pemesanan') }}" class="nav-link py-2 {{ request()->routeIs('pemilik.laporan.pemesanan') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text me-2"></i>Laporan Pemesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pemilik.laporan.pendapatan') }}" class="nav-link py-2 {{ request()->routeIs('pemilik.laporan.pendapatan') ? 'active' : '' }}">
                            <i class="bi bi-cash-stack me-2"></i>Laporan Pendapatan
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
    <div class="p-3 border-top border-secondary">
        <a href="{{ route('home') }}" class="btn btn-outline-light w-100 mb-2 rounded-pill btn-sm">
            <i class="bi bi-globe me-1"></i>Lihat Website
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn text-white w-100 rounded-pill btn-sm" style="background:#387478;">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 topbar">
        <button class="btn btn-dark d-lg-none" type="button" onclick="document.getElementById('pemilikSidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <div class="ms-auto d-flex align-items-center">
            <span class="fw-semibold me-3" style="color:#243642;">Halo, {{ Auth::user()->nama ?? 'Pemilik' }}</span>
            <span class="badge rounded-pill px-3 py-2 text-white" style="background:#629584;">Pemilik</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm no-print">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm no-print">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
@yield('js')
</body>
</html>
