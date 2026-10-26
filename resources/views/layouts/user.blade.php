<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Glamping Danau Kembar Scenery</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #F8FAFC; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            color: #1E293B;
        }
        .footer { margin-top: auto; }
        .card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(30, 41, 59, 0.08)!important; }
        .navbar-nav .nav-link { 
            white-space: nowrap !important; 
            font-size: 0.9rem; 
            font-weight: 600; 
            padding-left: 0.75rem !important; 
            padding-right: 0.75rem !important; 
            color: #334155;
        }
        .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover {
            color: #387478 !important;
        }
        footer a {
            color: rgba(241, 245, 249, 0.75) !important;
            transition: all 0.2s ease-in-out;
        }
        footer a:hover {
            color: #629584 !important;
            padding-left: 3px;
        }
        .navbar {
            z-index: 1050 !important;
        }
        .dropdown-menu {
            z-index: 1060 !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-xl navbar-light bg-white shadow-sm sticky-top py-2 border-bottom" style="border-color:#E2E8F0!important; z-index: 1050 !important;">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold me-3" href="{{ route('home') }}">
            <img src="{{ asset('images/logo1.jpg') }}" width="42" height="42" class="rounded-circle me-2 shadow-xs" style="object-fit:cover;">
            <span style="color:#243642; white-space:nowrap; font-weight:800; font-size:1.1rem;">Glamping Danau Kembar Scenery</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navUser">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navUser">
            <ul class="navbar-nav mx-auto align-items-center">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold' : '' }}" href="{{ route('home') }}">Beranda</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('tentang') ? 'active fw-bold' : '' }}" href="{{ route('tentang') }}">Tentang</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('glamping.*') ? 'active fw-bold' : '' }}" href="{{ route('glamping.index') }}">Unit Glamping</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('fasilitas') ? 'active fw-bold' : '' }}" href="{{ route('fasilitas') }}">Fasilitas</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('diskon') || request()->routeIs('promo') ? 'active fw-bold' : '' }}" href="{{ route('diskon') }}">Diskon</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('galeri') ? 'active fw-bold' : '' }}" href="{{ route('galeri') }}">Galeri</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('kontak') ? 'active fw-bold' : '' }}" href="{{ route('kontak') }}">Kontak</a></li>
            </ul>

            <div class="d-flex align-items-center mt-2 mt-xl-0 gap-2">
                @auth
                    @if(Auth::user()->role == 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn text-white rounded-pill me-2 btn-sm px-3 fw-semibold shadow-xs" style="background:#243642;">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                        </a>
                    @elseif(Auth::user()->role == 'pemilik')
                        <a href="{{ route('pemilik.dashboard') }}" class="btn text-white rounded-pill me-2 btn-sm px-3 fw-semibold shadow-xs" style="background:#243642;">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Pemilik
                        </a>
                    @elseif(Auth::user()->role == 'pengunjung')
                        @php
                            // Hanya pesanan aktif, maks 3
                            $userNotifs = \App\Models\Pemesanan::where('id_user', Auth::id())
                                            ->whereIn('status', ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Dikonfirmasi'])
                                            ->latest()
                                            ->take(3)
                                            ->get();
                            // Badge merah hanya untuk yang butuh tindakan segera
                            $urgentCount = $userNotifs->where('status', 'Menunggu Pembayaran')->count();
                        @endphp
                        {{-- Notifikasi --}}
                        <div class="dropdown me-2">
                            <button class="btn btn-light btn-sm rounded-circle p-2 border position-relative shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi Pesanan">
                                <i class="bi bi-bell-fill text-secondary"></i>
                                @if($urgentCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:10px;">
                                        {{ $urgentCount }}
                                    </span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-3" style="width:320px;max-height:400px;overflow-y:auto;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.9rem;">Notifikasi Pesanan</h6>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $userNotifs->count() }} Aktif</span>
                                </div>
                                @forelse($userNotifs as $n)
                                    <a href="{{ route('user.dashboard') }}" class="dropdown-item rounded-3 p-2 mb-1 text-wrap border-bottom border-light">
                                        <div class="d-flex align-items-start">
                                            @if($n->status == 'Dikonfirmasi')
                                                <i class="bi bi-check-circle-fill text-success fs-5 me-2 mt-1"></i>
                                                <div>
                                                    <strong class="d-block text-dark small">Pemesanan Dikonfirmasi</strong>
                                                    <small class="text-muted" style="font-size:11px;">#{{ $n->id_pemesanan }} - {{ $n->glamping->nama_glamping ?? 'Glamping' }}</small>
                                                </div>
                                            @elseif($n->status == 'Menunggu Verifikasi')
                                                <i class="bi bi-hourglass-split text-info fs-5 me-2 mt-1"></i>
                                                <div>
                                                    <strong class="d-block text-dark small">Bukti Transfer Diunggah</strong>
                                                    <small class="text-muted" style="font-size:11px;">Sedang diverifikasi oleh Admin</small>
                                                </div>
                                            @elseif($n->status == 'Menunggu Pembayaran')
                                                <i class="bi bi-clock-history text-warning fs-5 me-2 mt-1"></i>
                                                <div>
                                                    <strong class="d-block text-dark small">Menunggu Pembayaran DP</strong>
                                                    <small class="text-danger fw-semibold" style="font-size:11px;">Batas waktu 1 jam</small>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-3 text-muted small">Belum ada notifikasi pesanan</div>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-pill dropdown-toggle fw-bold px-3 py-2 border shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color:#243642;background:#fff;">
                            <i class="bi bi-person-circle me-1" style="color:#387478;"></i>{{ Auth::user()->nama }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 p-2" style="min-width:200px;">
                            @if(Auth::user()->role == 'pengunjung')
                                <li><a class="dropdown-item rounded-3 py-2 small fw-semibold" href="{{ route('user.dashboard') }}"><i class="bi bi-person-circle me-2 text-success"></i>Profil Saya</a></li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-3 py-2 text-danger small fw-semibold">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-dark rounded-pill btn-sm px-3 fw-semibold" style="border-color:#387478;color:#387478;">Login</a>
                    <a href="{{ route('register') }}" class="btn text-white rounded-pill btn-sm px-3 fw-semibold shadow-xs" style="background:#387478;">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="flex-grow-1">
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm p-3 mb-0" role="alert" style="background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0!important;">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm p-3 mb-0" role="alert" style="background:#FEF2F2;color:#991B1B;border:1px solid #FCA5A5!important;">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><strong>Pemberitahuan:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @yield('content')
</div>

{{-- Footer --}}
<footer class="footer pt-5 pb-4 text-white" style="background: #0F172A; border-top: 4px solid #387478;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('images/logo1.jpg') }}" width="45" height="45" class="rounded-circle me-3 shadow-sm" style="object-fit:cover;">
                    <div>
                        <h5 class="fw-bold mb-0 text-white">Glamping Danau Kembar</h5>
                        <small style="color:#629584; font-weight:600; font-size:0.8rem;">Scenery Resort &amp; Camping</small>
                    </div>
                </div>
                <p class="text-light opacity-75 small lh-base mb-0" style="max-width:340px;">
                    Nikmati pengalaman menginap mewah (glamour camping) terbaik di kawasan wisata Danau Kembar, Kabupaten Solok, Sumatera Barat dengan panorama danau &amp; pegunungan yang menakjubkan.
                </p>
            </div>

            <div class="col-lg-2 col-md-6 mb-3">
                <h6 class="fw-bold mb-3 text-uppercase small tracking-wider" style="color:#629584!important; letter-spacing: 1px;">NAVIGASI</h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                    <li class="mb-2"><a href="{{ route('tentang') }}" class="text-decoration-none">Tentang Kami</a></li>
                    <li class="mb-2"><a href="{{ route('glamping.index') }}" class="text-decoration-none">Unit Glamping</a></li>
                    <li class="mb-2"><a href="{{ route('fasilitas') }}" class="text-decoration-none">Fasilitas</a></li>
                    <li class="mb-2"><a href="{{ route('diskon') }}" class="text-decoration-none">Diskon Aktif</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <h6 class="fw-bold mb-3 text-uppercase small tracking-wider" style="color:#629584!important; letter-spacing: 1px;">TAUTAN LAINNYA</h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><a href="{{ route('galeri') }}" class="text-decoration-none">Galeri Momen</a></li>
                    <li class="mb-2"><a href="{{ route('kontak') }}" class="text-decoration-none">Kontak &amp; Lokasi</a></li>
                    @auth
                        <li class="mb-2"><a href="{{ route('user.dashboard') }}" class="text-decoration-none">Dashboard Saya</a></li>
                    @endauth
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <h6 class="fw-bold mb-3 text-uppercase small tracking-wider" style="color:#629584!important; letter-spacing: 1px;">KONTAK &amp; LOKASI</h6>
                <ul class="list-unstyled text-light opacity-75 small mb-3">
                    <li class="mb-2 d-flex"><i class="bi bi-geo-alt-fill text-danger me-2 flex-shrink-0 mt-1"></i>Danau Kembar, Kabupaten Solok, Sumatera Barat</li>
                    <li class="mb-2 d-flex"><i class="bi bi-telephone-fill text-success me-2 flex-shrink-0"></i>0813-7222-0567</li>
                    <li class="mb-2 d-flex"><i class="bi bi-envelope-fill text-info me-2 flex-shrink-0"></i>info@glampingdanaukembar.com</li>
                </ul>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" class="text-light"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-light"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-light"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
        </div>

        <div class="row pt-3 mt-4 text-light opacity-75 small border-top border-secondary border-opacity-25 justify-content-center text-center">
            <div class="col-12 text-center">
                <p class="mb-0">&copy; {{ date('Y') }} Glamping Danau Kembar Scenery. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
@yield('js')
</body>
</html>
