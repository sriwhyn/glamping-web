<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8faf8; }
        .sidebar { width: 260px; height: 100vh; overflow-y: auto; background: #243642; color: #fff; position: fixed; top: 0; left: 0; z-index: 1000; transition: .3s; }
        .sidebar .nav-link { color: #adb5bd; font-weight: 500; padding: 12px 20px; border-radius: 10px; margin: 4px 15px; transition: .3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: #387478; }
        .main-content { margin-left: 260px; padding: 30px; min-height: 100vh; }
        @media(max-width: 991px) {
            .sidebar { margin-left: -260px; }
            .sidebar.show { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-0" id="adminSidebar">
    <div class="p-4 d-flex align-items-center border-bottom border-secondary">
        <img src="{{ asset('images/logo1.jpg') }}" width="40" height="40" class="rounded-circle me-2">
        <h5 class="fw-bold mb-0 text-white fs-6">Admin Panel</h5>
    </div>
    <ul class="nav nav-pills flex-column mt-3 mb-auto">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.glamping.index') }}" class="nav-link {{ request()->routeIs('admin.glamping.*') ? 'active' : '' }}">
                <i class="bi bi-house-door me-2"></i>Unit Glamping
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.tipe.index') }}" class="nav-link {{ request()->routeIs('admin.tipe.*') ? 'active' : '' }}">
                <i class="bi bi-tags me-2"></i>Tipe Glamping
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.pemesanan.index') }}" class="nav-link {{ request()->routeIs('admin.pemesanan.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check me-2"></i>Pemesanan
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.pembayaran.index') }}" class="nav-link {{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card me-2"></i>Pembayaran
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.fasilitas.index') }}" class="nav-link {{ request()->routeIs('admin.fasilitas.*') ? 'active' : '' }}">
                <i class="bi bi-stars me-2"></i>Fasilitas Layanan
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.diskon.index') }}" class="nav-link {{ request()->routeIs('admin.diskon.*') || request()->routeIs('admin.promo.*') ? 'active' : '' }}">
                <i class="bi bi-percent me-2"></i> Diskon
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.user.index') }}" class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                <span><i class="bi bi-people me-2"></i>Data Pengunjung</span>
                @php
                    $pendingUsersSidebar = \App\Models\User::where('status_akun', 'pending')->count();
                @endphp
                @if($pendingUsersSidebar > 0)
                    <span class="badge bg-warning text-dark rounded-pill">{{ $pendingUsersSidebar }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.review.index') }}" class="nav-link {{ request()->routeIs('admin.review.*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-quote me-2"></i>Review
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.galeri.index') }}" class="nav-link {{ request()->routeIs('admin.galeri.*') ? 'active' : '' }}">
                <i class="bi bi-images me-2"></i>Galeri
            </a>
        </li>
        <li class="nav-item">
            <a href="#menuLaporanAdmin" class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('pemilik.laporan.*') ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('pemilik.laporan.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan</span>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <div class="collapse {{ request()->routeIs('pemilik.laporan.*') ? 'show' : '' }} ps-3" id="menuLaporanAdmin">
                <ul class="nav flex-column mt-1">
                    <li class="nav-item">
                        <a href="{{ route('pemilik.laporan.pemesanan') }}" class="nav-link py-2 {{ request()->routeIs('pemilik.laporan.pemesanan') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text me-2"></i>Laporan Pemesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pemilik.laporan.pendapatan') }}" class="nav-link py-2 {{ request()->routeIs('pemilik.laporan.pendapatan') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow me-2"></i>Laporan Pendapatan
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button class="btn btn-dark d-lg-none" type="button" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <div class="ms-auto d-flex align-items-center gap-3">
            @php
                $unverifiedPayments = \App\Models\Pemesanan::where('status', 'Menunggu Verifikasi')->count();
                $pendingOrders = \App\Models\Pemesanan::where('status', 'Menunggu Pembayaran')->count();
                $pendingUsersCount = \App\Models\User::where('status_akun', 'pending')->count();
                $unreadReschedules = \App\Models\Pemesanan::where('is_reschedule_notified', 0)->where('reschedule_count', '>', 0)->count();
                $totalAdminNotif = $unverifiedPayments + $pendingUsersCount + $unreadReschedules;
            @endphp
            {{-- Notifikasi --}}
            <div class="dropdown">
                <button class="btn btn-light rounded-circle p-2 border position-relative shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi Admin">
                    <i class="bi bi-bell-fill text-dark"></i>
                    @if($totalAdminNotif > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:10px;">
                            {{ $totalAdminNotif }}
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-3" style="width:320px;">
                    <h6 class="fw-bold mb-2 pb-2 border-bottom text-dark d-flex justify-content-between align-items-center" style="font-size:0.9rem;">
                        <span>Notifikasi Admin</span>
                        @if($totalAdminNotif > 0)
                            <span class="badge bg-danger rounded-pill" style="font-size:10px;">{{ $totalAdminNotif }} Baru</span>
                        @endif
                    </h6>
                    @if($unreadReschedules > 0)
                        <a href="{{ route('admin.pemesanan.index', ['filter_reschedule' => 'unread']) }}" class="dropdown-item rounded-3 p-2 mb-1 text-wrap border-bottom border-light bg-primary-subtle">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small fw-bold text-primary"><i class="bi bi-arrow-repeat me-1"></i>Reschedule Jadwal</span>
                                <span class="badge bg-primary text-white rounded-pill">{{ $unreadReschedules }}</span>
                            </div>
                            <small class="text-muted d-block mt-0.5" style="font-size:10.5px;">Ada perubahan tanggal dari pengunjung</small>
                        </a>
                    @endif
                    <a href="{{ route('admin.user.index') }}" class="dropdown-item rounded-3 p-2 mb-1 text-wrap border-bottom border-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-dark">Persetujuan Akun</span>
                            <span class="badge bg-warning text-dark rounded-pill">{{ $pendingUsersCount }}</span>
                        </div>
                    </a>
                    <a href="{{ route('admin.pembayaran.index') }}" class="dropdown-item rounded-3 p-2 mb-1 text-wrap border-bottom border-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-dark">Perlu Verifikasi Bayar</span>
                            <span class="badge bg-danger text-white rounded-pill">{{ $unverifiedPayments }}</span>
                        </div>
                    </a>
                    <a href="{{ route('admin.pemesanan.index') }}" class="dropdown-item rounded-3 p-2 text-wrap">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-dark">Belum Bayar</span>
                            <span class="badge bg-secondary text-white rounded-pill">{{ $pendingOrders }}</span>
                        </div>
                    </a>
                </div>
            </div>

            <span class="fw-semibold" style="color:#243642;">Halo, {{ Auth::user()->nama ?? 'Admin' }}</span>
            <span class="badge rounded-pill px-3 py-2 text-white" style="background:#387478;">Admin</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
@yield('js')
</body>
</html>
