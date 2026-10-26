@extends('layouts.user')

@section('title', 'Pemesanan Saya')

@section('content')
@php
    // Otomatis batalkan pesanan kadaluarsa dan selesaikan kunjungan yang telah checkout
    \App\Models\Pemesanan::cancelExpiredOrders();
    $user = Auth::user();
    $userPemesanans = \App\Models\Pemesanan::with(['glamping', 'pembayaran', 'diskon', 'promo', 'review'])
                        ->where('id_user', $user->id_user)
                        ->latest()
                        ->get();
                        
    $aktifPemesanans = $userPemesanans->whereIn('status', ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Dikonfirmasi']);
    $selesaiPemesanans = $userPemesanans->where('status', 'Selesai');
    $dibatalkanPemesanans = $userPemesanans->where('status', 'Dibatalkan');
@endphp

{{-- Banner Pemesanan --}}
<div class="py-4 py-md-5" style="background: linear-gradient(135deg, #1E293B 0%, #243642 100%);">
    <div class="container text-white">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold fs-2 text-white me-3 shadow flex-shrink-0" 
                         style="width:68px;height:68px;background:#387478;border:3px solid #629584;">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge px-3 py-1 rounded-pill" style="background:#629584; font-size:11px;">Reservasi Glamping</span>
                        </div>
                        <h3 class="fw-bold mb-1 text-white">Daftar Pemesanan Saya</h3>
                        <p class="text-light opacity-75 mb-0 small">
                            Kelola jadwal menginap, reschedule (maks 1x), ulasan pengunjung, dan riwayat kunjungan Anda.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('glamping.index') }}" class="btn text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background:#387478;">
                    <i class="bi bi-plus-circle me-1"></i>Pesan Glamping Baru
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-4 py-md-5">

    {{-- Notifikasi Denda Ringkas --}}
    @php
        $unpaidFines = $userPemesanans->filter(function($item) {
            return $item->denda_kerusakan > 0 && !str_contains($item->catatan_kerusakan ?? '', 'Lunas') && $item->status_deposit != 'Dikembalikan';
        });
    @endphp
    @if($unpaidFines->isNotEmpty())
        @foreach($unpaidFines as $fineBooking)
            <div class="alert alert-danger py-2.5 px-3 rounded-3 mb-3 border-danger-subtle d-flex flex-wrap align-items-center justify-content-between gap-2 shadow-xs" style="background:#FEF2F2;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-6 flex-shrink-0"></i>
                    <div class="small text-dark">
                        <strong>Denda Kerusakan:</strong> Rp {{ number_format($fineBooking->denda_kerusakan, 0, ',', '.') }} untuk pesanan <strong>#{{ $fineBooking->id_pemesanan }}</strong> ({{ $fineBooking->glamping->nama_glamping ?? 'Glamping' }})
                        @if($fineBooking->catatan_kerusakan)
                            <span class="text-muted ms-1">&bull; {{ $fineBooking->catatan_kerusakan }}</span>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1 fw-semibold btn-bayar-denda text-nowrap" data-id="{{ $fineBooking->id_pemesanan }}">
                    Bayar Denda
                </button>
            </div>
        @endforeach
    @endif

    {{-- Tab Status Pemesanan --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="border:1px solid #E2E8F0!important;">
        
        <div class="card-header bg-white p-3 border-bottom">
            <ul class="nav nav-pills card-header-pills gap-2" id="pemesananTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-bold px-3.5 py-2" id="tab-aktif-tab" data-bs-toggle="tab" data-bs-target="#tab-aktif" type="button" role="tab">
                        <i class="bi bi-clock-history me-1.5"></i>Pesanan Aktif 
                        <span class="badge bg-primary text-white rounded-pill ms-1.5">{{ $aktifPemesanans->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold px-3.5 py-2" id="tab-selesai-tab" data-bs-toggle="tab" data-bs-target="#tab-selesai" type="button" role="tab">
                        <i class="bi bi-check-circle me-1.5"></i>Selesai 
                        <span class="badge bg-success text-white rounded-pill ms-1.5">{{ $selesaiPemesanans->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold px-3.5 py-2" id="tab-dibatalkan-tab" data-bs-toggle="tab" data-bs-target="#tab-dibatalkan" type="button" role="tab">
                        <i class="bi bi-x-circle me-1.5"></i>Dibatalkan 
                        <span class="badge bg-secondary text-white rounded-pill ms-1.5">{{ $dibatalkanPemesanans->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="pemesananTabsContent">

                {{-- Pesanan Aktif --}}
                <div class="tab-pane fade show active" id="tab-aktif" role="tabpanel">
                    <div class="p-3">
                        @forelse($aktifPemesanans as $p)
                            <div class="card border shadow-xs rounded-4 mb-3 overflow-hidden bg-white" style="border-color:#E2E8F0!important;">
                                <div class="card-body p-3">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset($p->glamping->gambar ?? 'images/bg.jpeg') }}" class="rounded-3 me-3 flex-shrink-0" width="56" height="56" style="object-fit:cover;">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">{{ $p->glamping->nama_glamping ?? 'Unit Glamping' }}</h6>
                                                    <span class="badge bg-light text-dark border font-monospace px-2 py-0.5" style="font-size:10px;">#{{ $p->id_pemesanan }}</span>
                                                    @if(($p->reschedule_count ?? 0) > 0)
                                                        <span class="badge bg-purple-subtle text-primary border border-primary-subtle px-2 py-0.5" style="font-size:10px;"><i class="bi bi-arrow-repeat me-1"></i>Reschedule (1x)</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted d-block">
                                                    Tipe: {{ $p->glamping->tipeGlamping->nama_tipe ?? '-' }} &bull; {{ $p->jumlah_tamu }} Tamu &bull; Total: <strong class="text-dark">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong> 
                                                    @if($p->status_pelunasan == 'Lunas')
                                                        <span class="text-success fw-bold">(Lunas 100%)</span>
                                                    @else
                                                        <span class="text-muted">(DP: Rp {{ number_format($p->dibayar, 0, ',', '.') }} &bull; Sisa: Rp {{ number_format($p->sisa_bayar, 0, ',', '.') }})</span>
                                                    @endif
                                                </small>
                                                <small class="text-muted d-block mt-0.5" style="font-size:11px;">
                                                    <i class="bi bi-calendar-event me-1 text-primary"></i>Menginap: {{ date('d M Y', strtotime($p->tanggal_checkin)) }} &ndash; {{ date('d M Y', strtotime($p->tanggal_checkout)) }}
                                                </small>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            @if($p->pembayaran && $p->pembayaran->status_verifikasi == 'Ditolak')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-semibold" style="font-size:11px;">Pembayaran Ditolak</span>
                                            @elseif($p->status == 'Dikonfirmasi')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold" style="font-size:11px;">Dikonfirmasi</span>
                                            @elseif($p->status == 'Menunggu Verifikasi')
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1.5 rounded-pill fw-semibold" style="font-size:11px;">Menunggu Verifikasi</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5 rounded-pill fw-semibold" style="font-size:11px;">Menunggu Pembayaran</span>
                                            @endif

                                            @if($p->status_pelunasan == 'Lunas')
                                                <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-bold" style="font-size:11px;">Lunas 100%</span>
                                            @elseif(in_array($p->status, ['Dikonfirmasi', 'Menunggu Verifikasi']))
                                                <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill fw-semibold" style="font-size:11px;">DP 50%</span>
                                            @endif

                                            @if($p->denda_kerusakan > 0 && !str_contains($p->catatan_kerusakan ?? '', 'Lunas') && $p->status_deposit != 'Dikembalikan')
                                                <span class="badge bg-danger text-white px-2.5 py-1.5 rounded-pill fw-semibold" style="font-size:11px;">Ada Denda</span>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-2 justify-content-end flex-wrap">
                                            @if($p->status == 'Menunggu Pembayaran')
                                                @if($p->pembayaran && $p->pembayaran->status_verifikasi == 'Ditolak')
                                                    <a href="{{ route('pembayaran.create', $p->id_pemesanan) }}" class="btn btn-danger btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                                                        <i class="bi bi-arrow-clockwise me-1"></i>Upload Ulang Bukti
                                                    </a>
                                                @else
                                                    <a href="{{ route('pembayaran.create', $p->id_pemesanan) }}" class="btn text-white btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-xs" style="background:#387478;">Bayar DP</a>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCancel{{ $p->id_pemesanan }}">Batalkan</button>
                                            @elseif(in_array($p->status, ['Dikonfirmasi', 'Menunggu Verifikasi']))
                                                @if(($p->reschedule_count ?? 0) < 1)
                                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalReschedule{{ $p->id_pemesanan }}">
                                                        <i class="bi bi-calendar-range me-1"></i>Reschedule
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-light btn-sm border rounded-pill px-3 py-1.5 fw-semibold text-muted" disabled title="Batas reschedule 1x sudah tercapai">
                                                        <i class="bi bi-check2-all me-1 text-success"></i>Reschedule (1/1)
                                                    </button>
                                                @endif
                                                @if($p->status == 'Dikonfirmasi' && $p->status_pelunasan != 'Lunas')
                                                    <button type="button" class="btn text-white btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-xs btn-bayar-pelunasan" style="background:#387478;" data-id="{{ $p->id_pemesanan }}">
                                                        <i class="bi bi-credit-card me-1"></i>Pelunasan Online
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCancel{{ $p->id_pemesanan }}">Batalkan</button>
                                            @endif
                                            <button type="button" class="btn btn-light btn-sm border rounded-pill px-3 py-1.5 fw-semibold text-dark" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $p->id_pemesanan }}">Detail</button>
                                        </div>
                                    </div>

                                    {{-- Banner Notifikasi Denda Kerusakan di Luar Card --}}
                                    @if($p->denda_kerusakan > 0 && !str_contains($p->catatan_kerusakan ?? '', 'Lunas') && $p->status_deposit != 'Dikembalikan')
                                        <div class="mt-2.5 p-2 px-3 rounded-3 bg-light border border-danger-subtle d-flex flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="small">
                                                <span class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>Denda Kerusakan: Rp {{ number_format($p->denda_kerusakan, 0, ',', '.') }}</span>
                                                @if($p->catatan_kerusakan)
                                                    <span class="text-muted ms-1">({{ $p->catatan_kerusakan }})</span>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1 fw-semibold shadow-xs btn-bayar-denda text-nowrap" data-id="{{ $p->id_pemesanan }}">
                                                Bayar Denda
                                            </button>
                                        </div>
                                    @endif

                                    @if($p->pembayaran && $p->pembayaran->status_verifikasi == 'Ditolak')
                                        <div class="mt-2.5 alert alert-danger p-2 px-3 mb-0 rounded-3 small border-0 d-flex align-items-center" style="background:#FEF2F2;color:#991B1B;font-size:0.8rem;">
                                            <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0 fs-6"></i>
                                            <div><strong>Pembayaran Sebelumnya Ditolak Admin:</strong> Bukti transfer yang Anda kirimkan sebelumnya tidak sesuai atau kurang jelas.</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 bg-white rounded-4 p-4 border">
                                <h6 class="fw-bold text-dark mb-1">Belum ada pesanan glamping yang sedang aktif.</h6>
                                <small class="text-muted d-block mb-3">Temukan tempat menginap terbaik untuk liburan Anda.</small>
                                <a href="{{ route('glamping.index') }}" class="btn text-white rounded-pill px-4 py-2 fw-semibold" style="background:#387478;">Cari Glamping</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Riwayat Selesai --}}
                <div class="tab-pane fade" id="tab-selesai" role="tabpanel">
                    <div class="p-3">
                        @forelse($selesaiPemesanans as $p)
                            <div class="card border shadow-xs rounded-4 mb-3 overflow-hidden bg-white" style="border-color:#E2E8F0!important;">
                                <div class="card-body p-3">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset($p->glamping->gambar ?? 'images/bg.jpeg') }}" class="rounded-3 me-3 flex-shrink-0" width="56" height="56" style="object-fit:cover;">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">{{ $p->glamping->nama_glamping ?? 'Unit Glamping' }}</h6>
                                                    <span class="badge bg-light text-dark border font-monospace px-2 py-0.5" style="font-size:10px;">#{{ $p->id_pemesanan }}</span>
                                                </div>
                                                <small class="text-muted d-block">
                                                    Tipe: {{ $p->glamping->tipeGlamping->nama_tipe ?? '-' }} &bull; {{ $p->jumlah_tamu }} Tamu &bull; Total: <strong class="text-dark">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                                                    @if($p->status_pelunasan == 'Lunas')
                                                        <span class="text-success fw-bold">(Lunas 100%)</span>
                                                    @endif
                                                </small>
                                                <small class="text-muted d-block mt-0.5" style="font-size:11px;">
                                                    <i class="bi bi-calendar-event me-1 text-primary"></i>Menginap: {{ date('d M Y', strtotime($p->tanggal_checkin)) }} &ndash; {{ date('d M Y', strtotime($p->tanggal_checkout)) }}
                                                </small>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold" style="font-size:11px;">Selesai</span>
                                            @if($p->status_pelunasan == 'Lunas')
                                                <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-bold" style="font-size:11px;">Lunas 100%</span>
                                            @else
                                                <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill fw-semibold" style="font-size:11px;">DP 50%</span>
                                            @endif

                                            @if($p->denda_kerusakan > 0 && !str_contains($p->catatan_kerusakan ?? '', 'Lunas') && $p->status_deposit != 'Dikembalikan')
                                                <span class="badge bg-danger text-white px-2.5 py-1.5 rounded-pill fw-semibold" style="font-size:11px;">Ada Denda</span>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-2 justify-content-end flex-wrap">
                                            @if($p->review)
                                                <button type="button" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-3 py-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalReview{{ $p->id_pemesanan }}">
                                                    <i class="bi bi-star-fill text-warning me-1"></i>Ulasan Diberikan ({{ $p->review->rating }}★)
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-warning text-dark btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-xs" data-bs-toggle="modal" data-bs-target="#modalReview{{ $p->id_pemesanan }}">
                                                    <i class="bi bi-star-fill me-1"></i>Beri Ulasan & Rating
                                                </button>
                                            @endif

                                            @if($p->glamping && $p->glamping->status == 'tersedia')
                                                <a href="{{ route('glamping.show', $p->id_glamping) }}" class="btn text-white btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-xs" style="background:#387478;">Pesan Lagi</a>
                                            @endif
                                            <button type="button" class="btn btn-light btn-sm border rounded-pill px-3 py-1.5 fw-semibold text-dark" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $p->id_pemesanan }}">Detail</button>
                                        </div>
                                    </div>

                                    {{-- Banner Notifikasi Denda Kerusakan di Luar Card Selesai --}}
                                    @if($p->denda_kerusakan > 0 && !str_contains($p->catatan_kerusakan ?? '', 'Lunas') && $p->status_deposit != 'Dikembalikan')
                                        <div class="mt-2.5 p-2 px-3 rounded-3 bg-light border border-danger-subtle d-flex flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="small">
                                                <span class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>Denda Kerusakan: Rp {{ number_format($p->denda_kerusakan, 0, ',', '.') }}</span>
                                                @if($p->catatan_kerusakan)
                                                    <span class="text-muted ms-1">({{ $p->catatan_kerusakan }})</span>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1 fw-semibold shadow-xs btn-bayar-denda text-nowrap" data-id="{{ $p->id_pemesanan }}">
                                                Bayar Denda
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 bg-white rounded-4 p-4 border">
                                <h6 class="fw-bold text-dark mb-1">Belum ada riwayat kunjungan yang telah selesai.</h6>
                                <small class="text-muted d-block mb-3">Temukan tempat menginap terbaik untuk liburan Anda.</small>
                                <a href="{{ route('glamping.index') }}" class="btn text-white rounded-pill px-4 py-2 fw-semibold" style="background:#387478;">Cari Glamping</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Pesanan Dibatalkan --}}
                <div class="tab-pane fade" id="tab-dibatalkan" role="tabpanel">
                    <div class="p-3">
                        @if($dibatalkanPemesanans->count() > 0)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">Total {{ $dibatalkanPemesanans->count() }} pesanan dibatalkan / expired</small>
                                <form action="{{ route('pemesanan.destroy_all_cancelled') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA riwayat pesanan yang dibatalkan / expired?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-trash me-1"></i>Bersihkan Semua Riwayat Dibatalkan
                                    </button>
                                </form>
                            </div>
                        @endif

                        @forelse($dibatalkanPemesanans as $p)
                            <div class="card border shadow-xs rounded-4 mb-3 overflow-hidden bg-white" style="border-color:#E2E8F0!important;">
                                <div class="card-body p-3">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset($p->glamping->gambar ?? 'images/bg.jpeg') }}" class="rounded-3 me-3 flex-shrink-0" width="56" height="56" style="object-fit:cover;">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">{{ $p->glamping->nama_glamping ?? 'Unit Glamping' }}</h6>
                                                    <span class="badge bg-light text-dark border font-monospace px-2 py-0.5" style="font-size:10px;">#{{ $p->id_pemesanan }}</span>
                                                </div>
                                                <small class="text-muted d-block">
                                                    Tipe: {{ $p->glamping->tipeGlamping->nama_tipe ?? '-' }} &bull; {{ $p->jumlah_tamu }} Tamu &bull; Total: <strong class="text-dark">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                                                </small>
                                                <small class="text-muted d-block mt-0.5" style="font-size:11px;">
                                                    <i class="bi bi-calendar-event me-1 text-primary"></i>Menginap: {{ date('d M Y', strtotime($p->tanggal_checkin)) }} &ndash; {{ date('d M Y', strtotime($p->tanggal_checkout)) }}
                                                </small>
                                            </div>
                                        </div>

                                        <div>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-semibold" style="font-size:11px;"><i class="bi bi-x-circle-fill me-1"></i>Dibatalkan</span>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 justify-content-end flex-wrap">
                                            @if($p->glamping && $p->glamping->status == 'tersedia')
                                                <a href="{{ route('glamping.show', $p->id_glamping) }}" class="btn text-white btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-xs" style="background:#387478;">Pesan Lagi</a>
                                            @endif
                                            <button type="button" class="btn btn-light btn-sm border rounded-pill px-3 py-1.5 fw-semibold text-dark" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $p->id_pemesanan }}">Detail</button>
                                            <form action="{{ route('pemesanan.destroy', $p->id_pemesanan) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat pemesanan dibatalkan #{{ $p->id_pemesanan }} ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5 fw-semibold">
                                                    <i class="bi bi-trash me-1"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 bg-white rounded-4 p-4 border">
                                <h6 class="fw-bold text-dark mb-1">Tidak ada pesanan yang dibatalkan.</h6>
                                <small class="text-muted d-block mb-3">Temukan tempat menginap terbaik untuk liburan Anda.</small>
                                <a href="{{ route('glamping.index') }}" class="btn text-white rounded-pill px-4 py-2 fw-semibold" style="background:#387478;">Cari Glamping</a>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Pemesanan --}}
@foreach($userPemesanans as $p)
    {{-- Modal Pembatalan --}}
    @if(in_array($p->status, ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Dikonfirmasi']))
        <div class="modal fade" id="modalCancel{{ $p->id_pemesanan }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-3 px-4">
                        <h6 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-1.5"></i>Batalkan Reservasi #{{ $p->id_pemesanan }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <p class="text-dark fw-semibold mb-2">Apakah Anda yakin ingin membatalkan pemesanan unit <strong>{{ $p->glamping->nama_glamping ?? 'Glamping' }}</strong>?</p>
                        
                        @if(in_array($p->status, ['Menunggu Verifikasi', 'Dikonfirmasi']))
                            <div class="alert alert-danger rounded-3 text-start small p-3 mb-3 border-danger">
                                <i class="bi bi-exclamation-octagon-fill me-1 fw-bold"></i><strong>PERHATIAN KETENTUAN DP HANGUS:</strong><br>
                                Karena Anda telah membayarkan Uang Muka (DP 50%), pembatalan pesanan ini akan menyebabkan <strong>DP yang telah ditransfer HANGUS (tidak dapat dikembalikan/non-refundable)</strong>.
                            </div>
                        @else
                            <p class="small text-muted mb-3">Pesanan yang belum dibayar ini akan dibatalkan secara permanen.</p>
                        @endif

                        <form action="{{ route('pembayaran.batalkan', $p->id_pemesanan) }}" method="POST">
                            @csrf
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light w-50 rounded-pill fw-semibold border" data-bs-dismiss="modal">Tidak, Kembali</button>
                                <button type="submit" class="btn btn-danger w-50 rounded-pill fw-bold">Ya, Batalkan Pesanan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Reschedule --}}
    @if(in_array($p->status, ['Dikonfirmasi', 'Menunggu Verifikasi']) && ($p->reschedule_count ?? 0) < 1)
        <div class="modal fade" id="modalReschedule{{ $p->id_pemesanan }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                    <form action="{{ route('pemesanan.reschedule', $p->id_pemesanan) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom py-3 px-4" style="background: linear-gradient(135deg, #1E293B 0%, #387478 100%);">
                            <h5 class="modal-title fw-bold text-white fs-6">
                                <i class="bi bi-calendar-range me-2"></i>Ubah Jadwal (Reschedule) #{{ $p->id_pemesanan }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            <div class="alert alert-info rounded-3 small p-3 mb-3 border-info">
                                <i class="bi bi-info-circle-fill me-1"></i><strong>Ketentuan Reschedule:</strong><br>
                                Reschedule hanya dapat dilakukan <strong>maksimal 1 kali</strong>. Pastikan Anda memilih tanggal check-in dan check-out baru yang sesuai.
                            </div>

                            <div class="p-3 rounded-3 bg-light border mb-3">
                                <small class="text-muted d-block">Jadwal Menginap Saat Ini:</small>
                                <strong class="text-dark d-block"><i class="bi bi-calendar-event me-1 text-primary"></i>{{ date('d M Y', strtotime($p->tanggal_checkin)) }} &ndash; {{ date('d M Y', strtotime($p->tanggal_checkout)) }}</strong>
                                <small class="text-muted">{{ $p->jumlah_unit_pesan }} Unit &bull; {{ $p->jumlah_tamu }} Tamu &bull; Total Saat Ini: <strong>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong></small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark">Tanggal Check-in Baru</label>
                                <input type="date" name="tanggal_checkin" id="in_{{ $p->id_pemesanan }}" class="form-control rounded-3 py-2 checkin-reschedule" required min="{{ date('Y-m-d') }}" value="{{ $p->tanggal_checkin }}" data-id="{{ $p->id_pemesanan }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark">Tanggal Check-out Baru</label>
                                <input type="date" name="tanggal_checkout" id="out_{{ $p->id_pemesanan }}" class="form-control rounded-3 py-2 checkout-reschedule" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ $p->tanggal_checkout }}" data-id="{{ $p->id_pemesanan }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark">Jumlah Unit Kamar</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-door-open"></i></span>
                                    <input type="number" name="jumlah_unit_pesan" id="unit_{{ $p->id_pemesanan }}" class="form-control rounded-end-3 py-2 unit-reschedule" required min="1" max="{{ $p->glamping->jumlah_unit ?? 10 }}" value="{{ $p->jumlah_unit_pesan ?? 1 }}" data-id="{{ $p->id_pemesanan }}">
                                </div>
                                <small class="text-muted" style="font-size:11px;">Maksimal {{ $p->glamping->jumlah_unit ?? 1 }} unit sesuai total stok tipe ini.</small>
                            </div>

                            {{-- Summary Box Kalkulasi Real-time --}}
                            <div class="p-3 rounded-3 bg-white border border-primary-subtle mb-3" id="calc_box_{{ $p->id_pemesanan }}"
                                data-harga="{{ $p->glamping->harga ?? 0 }}"
                                data-unit="{{ $p->jumlah_unit_pesan ?? 1 }}"
                                data-extrabed-count="{{ $p->jumlah_extrabed }}"
                                data-extrabed-harga="{{ ($p->glamping && $p->glamping->harga_extrabed > 0) ? $p->glamping->harga_extrabed : 150000 }}"
                                data-diskon="{{ $p->diskon->nilai_diskon ?? 0 }}"
                                data-total-lama="{{ $p->total_harga }}">
                                <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-calculator me-1 text-primary"></i>Rincian Estimasi Biaya Jadwal Baru</h6>
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Durasi Menginap Baru:</span>
                                    <strong class="text-dark" id="calc_malam_{{ $p->id_pemesanan }}">- Malam</strong>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Jumlah Kamar:</span>
                                    <strong class="text-dark" id="calc_unit_{{ $p->id_pemesanan }}">{{ $p->jumlah_unit_pesan ?? 1 }} Unit</strong>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Estimasi Total Harga Baru:</span>
                                    <strong class="text-dark" id="calc_total_{{ $p->id_pemesanan }}">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between small text-muted pt-1 border-top mt-1">
                                    <span>Selisih Pembayaran:</span>
                                    <strong class="text-primary" id="calc_selisih_{{ $p->id_pemesanan }}">Rp 0 (Tetap)</strong>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-top py-3 px-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-xs" style="background:#387478;">
                                Simpan Jadwal Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Ulasan --}}
    @if($p->status == 'Selesai')
        <div class="modal fade" id="modalReview{{ $p->id_pemesanan }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                    <form action="{{ route('review.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_pemesanan" value="{{ $p->id_pemesanan }}">
                        <input type="hidden" name="id_glamping" value="{{ $p->id_glamping }}">

                        <div class="modal-header border-bottom py-3 px-4" style="background: linear-gradient(135deg, #1E293B 0%, #387478 100%);">
                            <h5 class="modal-title fw-bold text-white fs-6">
                                <i class="bi bi-star-fill me-2 text-warning"></i>Ulasan & Rating Reservasi #{{ $p->id_pemesanan }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            <div class="d-flex align-items-center mb-3 p-3 rounded-3 bg-light border">
                                <img src="{{ asset($p->glamping->gambar ?? 'images/bg.jpeg') }}" class="rounded-3 me-3" width="50" height="50" style="object-fit:cover;">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">{{ $p->glamping->nama_glamping ?? 'Unit Glamping' }}</h6>
                                    <small class="text-muted">Kunjungan: {{ date('d M Y', strtotime($p->tanggal_checkin)) }} &ndash; {{ date('d M Y', strtotime($p->tanggal_checkout)) }}</small>
                                </div>
                            </div>

                            <div class="mb-3 text-center">
                                <label class="form-label fw-bold text-dark d-block mb-2">Beri Rating Penilaian Anda</label>
                                <div class="rating-select d-flex justify-content-center gap-3 fs-3 text-warning">
                                    @for($s = 1; $s <= 5; $s++)
                                        <div class="form-check form-check-inline m-0 p-0">
                                            <input class="btn-check" type="radio" name="rating" id="star_{{ $p->id_pemesanan }}_{{ $s }}" value="{{ $s }}" {{ ($p->review->rating ?? 5) == $s ? 'checked' : '' }} required>
                                            <label class="btn btn-outline-warning border-0 rounded-circle p-2 fs-4 cursor-pointer" for="star_{{ $p->id_pemesanan }}_{{ $s }}" title="{{ $s }} Bintang">
                                                <i class="bi bi-star-fill"></i>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <small class="text-muted d-block mt-1">Pilih 1 (Sangat Buruk) hingga 5 (Sangat Puas)</small>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold small text-dark">Ulasan & Kesan Menginap</label>
                                <textarea name="komentar" class="form-control rounded-3" rows="4" placeholder="Ceritakan pengalaman Anda selama menginap di sini..." required>{{ $p->review->komentar ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="modal-footer border-top py-3 px-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-xs" style="background:#387478;">
                                Kirim Ulasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail{{ $p->id_pemesanan }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                
                <div class="modal-header bg-dark text-white py-3 px-4 border-bottom">
                    <h5 class="modal-title fw-bold text-white fs-6">
                        <i class="bi bi-receipt me-2"></i>Rincian Reservasi #{{ $p->id_pemesanan }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    
                    @if($p->glamping)
                        <div class="d-flex align-items-center mb-4 p-3 rounded-3 bg-light border">
                            <img src="{{ asset($p->glamping->gambar ?? 'images/bg.jpeg') }}" class="rounded-3 me-3 flex-shrink-0" width="70" height="70" style="object-fit:cover;">
                            <div>
                                <span class="badge bg-secondary mb-1">
                                    {{ $p->glamping->tipeGlamping->nama_tipe ?? 'Unit Glamping' }}
                                </span>
                                <h6 class="fw-bold mb-0 text-dark">{{ $p->glamping->nama_glamping }}</h6>
                                <small class="text-muted">Rp {{ number_format($p->glamping->harga, 0, ',', '.') }} / malam</small>
                            </div>
                        </div>
                    @endif
                    
                    <div class="row g-3 mb-3 small">
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Check-in:</span>
                            <div class="fw-bold text-dark"><i class="bi bi-calendar-check text-success me-1"></i>{{ date('d M Y', strtotime($p->tanggal_checkin)) }}</div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $p->jam_checkin ?? '14:00 WIB' }}</small>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Check-out:</span>
                            <div class="fw-bold text-dark"><i class="bi bi-calendar-x text-danger me-1"></i>{{ date('d M Y', strtotime($p->tanggal_checkout)) }}</div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>12:00 WIB</small>
                        </div>
                        <div class="col-6 mt-3">
                            <span class="text-muted d-block mb-1">Jumlah Tamu:</span>
                            <div class="fw-bold text-dark"><i class="bi bi-people me-1" style="color:#387478;"></i>{{ $p->jumlah_tamu }} Tamu</div>
                        </div>
                        <div class="col-6 mt-3">
                            <span class="text-muted d-block mb-1">Status Reservasi:</span>
                            @if($p->status == 'Dikonfirmasi')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold">Dikonfirmasi</span>
                            @elseif($p->status == 'Selesai')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold">Selesai</span>
                            @elseif($p->status == 'Dibatalkan')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-semibold">Dibatalkan</span>
                            @elseif($p->status == 'Menunggu Verifikasi')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill fw-semibold">Menunggu Verifikasi</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill fw-semibold">Menunggu Pembayaran</span>
                            @endif
                        </div>
                    </div>

                    @if(($p->reschedule_count ?? 0) > 0)
                        <div class="mb-3">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
                                <i class="bi bi-arrow-repeat me-1"></i>Jadwal Telah Diperbarui (1x Reschedule)
                            </span>
                        </div>
                    @endif
                    
                    <hr class="my-3">

                    <div class="p-3 rounded-4 bg-light border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Total Tagihan:</span>
                            <strong class="fw-bold text-dark">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">DP Terbayar:</span>
                            <strong class="fw-bold text-success">Rp {{ number_format($p->dibayar, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Status Pelunasan:</span>
                            @if($p->status_pelunasan == 'Lunas')
                                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-semibold">Lunas</span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2.5 py-1 rounded-pill fw-semibold">Belum Lunas</span>
                            @endif
                        </div>
                        @if($p->status_pelunasan == 'Lunas' && $p->metode_pelunasan)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Metode Pelunasan:</span>
                                <strong class="text-dark small">{{ $p->metode_pelunasan }}</strong>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted small">Sisa Pembayaran:</span>
                            @if($p->status_pelunasan == 'Lunas' || $p->sisa_bayar == 0)
                                <strong class="text-success fw-bold">Rp 0 (Lunas)</strong>
                            @else
                                <strong class="text-danger fw-bold">Rp {{ number_format($p->sisa_bayar, 0, ',', '.') }}</strong>
                            @endif
                        </div>
                    </div>

                    {{-- Banner Konfirmasi Lunas --}}
                    @if($p->status_pelunasan == 'Lunas')
                        <div class="mt-3 alert alert-success rounded-3 small p-3 border-0 d-flex align-items-center mb-0" style="background:#DCFCE7;color:#166534;">
                            <i class="bi bi-check-circle-fill me-2 fs-5 flex-shrink-0"></i>
                            <div>
                                <strong>Tagihan Lunas:</strong> Seluruh pembayaran sewa telah selesai dibayarkan.
                            </div>
                        </div>
                    @else
                        {{-- Pilihan Pelunasan Tagihan (Cash / Online) jika belum lunas --}}
                        @if(in_array($p->status, ['Dikonfirmasi', 'Selesai']) && $p->status_pelunasan != 'Lunas' && $p->sisa_bayar > 0)
                            <div class="mt-3 p-3 rounded-3 bg-light border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-semibold text-dark">Sisa Tagihan Pelunasan:</span>
                                    <strong class="text-danger fs-6">Rp {{ number_format($p->sisa_bayar, 0, ',', '.') }}</strong>
                                </div>
                                <p class="small text-muted mb-2.5">
                                    Pelunasan dapat dibayarkan secara tunai di resepsionis saat tiba, atau dilunasi langsung sekarang melalui transfer online.
                                </p>
                                @if($p->status == 'Dikonfirmasi')
                                    <button type="button" class="btn text-white w-100 rounded-pill py-2 fw-semibold shadow-xs btn-bayar-pelunasan" style="background:#387478;" data-id="{{ $p->id_pemesanan }}">
                                        <i class="bi bi-credit-card me-1.5"></i>Bayar Pelunasan Online Sekarang
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endif

                    {{-- Jaminan KTP Ringkas --}}
                    @if($p->status == 'Dikonfirmasi' && (!$p->status_deposit || in_array($p->status_deposit, ['Belum Bayar', 'Belum Diserahkan'])))
                        <div class="mt-3 p-2.5 px-3 rounded-3 bg-light border small text-muted d-flex align-items-center gap-2">
                            <i class="bi bi-person-badge text-secondary fs-6 flex-shrink-0"></i>
                            <div>Bawa <strong>KTP fisik</strong> sebagai jaminan saat check-in di resepsionis.</div>
                        </div>
                    @endif

                    {{-- Status Jaminan KTP & Kerusakan Barang --}}
                    @if($p->status_deposit && !in_array($p->status_deposit, ['Belum Bayar', 'Belum Diserahkan']))
                        <div class="p-3 rounded-3 bg-light border mt-3">
                            <h6 class="fw-bold text-dark small mb-2">Jaminan KTP & Fasilitas</h6>
                            <div class="d-flex justify-content-between align-items-center mb-1 small">
                                <span class="text-muted">Status KTP:</span>
                                @if($p->status_deposit == 'Dikembalikan')
                                    <span class="badge bg-success-subtle text-success border px-2 py-0.5 rounded-pill">Sudah Dikembalikan</span>
                                @elseif($p->status_deposit == 'Ditahan')
                                    <span class="badge bg-warning-subtle text-dark border px-2 py-0.5 rounded-pill">Ditahan (Sedang Menginap)</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5 rounded-pill">{{ $p->status_deposit }}</span>
                                @endif
                            </div>
                            @if($p->denda_kerusakan > 0)
                                <div class="d-flex justify-content-between align-items-center mb-1 small text-danger">
                                    <span>Denda Kerusakan:</span>
                                    <strong>+Rp {{ number_format($p->denda_kerusakan, 0, ',', '.') }}</strong>
                                </div>
                                @if($p->status_deposit == 'Dikembalikan' || str_contains($p->catatan_kerusakan ?? '', 'Lunas'))
                                    <div class="mt-2 pt-2 border-top">
                                        <span class="badge bg-success px-3 py-1.5 rounded-pill fw-semibold">Denda Selesai / Lunas</span>
                                    </div>
                                @else
                                    <div class="mt-2 pt-2 border-top">
                                        <button type="button" class="btn text-white w-100 rounded-pill py-2 fw-semibold shadow-xs btn-bayar-denda" style="background:#dc3545;" data-id="{{ $p->id_pemesanan }}">
                                            <i class="bi bi-credit-card me-1.5"></i>Bayar Denda Rp {{ number_format($p->denda_kerusakan, 0, ',', '.') }}
                                        </button>
                                    </div>
                                @endif
                            @endif
                            @if($p->catatan_kerusakan)
                                <div class="mt-2 pt-2 border-top small text-muted">
                                    <strong>Catatan:</strong> {{ $p->catatan_kerusakan }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Detail Pembayaran --}}
                    @if($p->pembayaran)
                        <div class="p-3 rounded-3 bg-light border mt-3">
                            <h6 class="fw-bold text-dark small mb-2.5">Detail Pembayaran Awal</h6>
                            <div class="d-flex justify-content-between align-items-center mb-1.5 small">
                                <span class="text-muted">Metode:</span>
                                <strong class="text-dark">{{ str_replace(['Midtrans Instant Payment - ', 'Instant Payment - '], '', $p->pembayaran->metode_pembayaran) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1.5 small">
                                <span class="text-muted">Jumlah Dibayar:</span>
                                <strong class="text-success">Rp {{ number_format($p->pembayaran->jumlah_bayar, 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1.5 small">
                                <span class="text-muted">Tanggal:</span>
                                <strong class="text-dark">{{ date('d M Y', strtotime($p->pembayaran->tanggal_pembayaran)) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-muted">Status:</span>
                                @if($p->pembayaran->status_verifikasi == 'Diterima')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill">Berhasil</span>
                                @elseif($p->pembayaran->status_verifikasi == 'Menunggu')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5 rounded-pill">Menunggu Verifikasi</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill">{{ $p->pembayaran->status_verifikasi }}</span>
                                @endif
                            </div>
                            @if($p->pembayaran->bukti_pembayaran && $p->pembayaran->bukti_pembayaran != 'images/bg.jpeg')
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted d-block mb-2">Bukti Pembayaran:</small>
                                    <img src="{{ asset($p->pembayaran->bukti_pembayaran) }}" class="rounded-3 border" style="max-width:100%; max-height:200px; object-fit:contain;" alt="Bukti Pembayaran">
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    @if($p->status == 'Menunggu Pembayaran')
                        <a href="{{ route('pembayaran.create', $p->id_pemesanan) }}" class="btn text-white rounded-pill px-4 fw-bold shadow-xs" style="background:#387478;">
                            <i class="bi bi-wallet2 me-1.5"></i>Bayar DP Sekarang
                        </a>
                    @elseif($p->status == 'Dibatalkan' || $p->status == 'Selesai')
                        @if($p->glamping && $p->glamping->status == 'tersedia')
                            <a href="{{ route('glamping.show', $p->id_glamping) }}" class="btn text-white rounded-pill px-4 fw-bold shadow-xs" style="background:#387478;">
                                <i class="bi bi-cart-plus me-1.5"></i>Pesan Glamping Ini Lagi
                            </a>
                        @endif
                    @endif
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function recalculate(idPemesanan) {
        const inEl = document.getElementById('in_' + idPemesanan);
        const outEl = document.getElementById('out_' + idPemesanan);
        const unitEl = document.getElementById('unit_' + idPemesanan);
        const calcBox = document.getElementById('calc_box_' + idPemesanan);

        if (!inEl || !outEl || !calcBox) return;

        const valIn = inEl.value;
        const valOut = outEl.value;

        if (!valIn || !valOut) return;

        const dIn = new Date(valIn);
        const dOut = new Date(valOut);
        const diffTime = dOut - dIn;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        const elemMalam = document.getElementById('calc_malam_' + idPemesanan);
        const elemUnit = document.getElementById('calc_unit_' + idPemesanan);
        const elemTotal = document.getElementById('calc_total_' + idPemesanan);
        const elemSelisih = document.getElementById('calc_selisih_' + idPemesanan);

        if (diffDays <= 0) {
            if (elemMalam) elemMalam.textContent = 'Tanggal Tidak Valid!';
            if (elemTotal) elemTotal.textContent = '-';
            if (elemSelisih) elemSelisih.textContent = '-';
            return;
        }

        const hargaPerMalam = parseFloat(calcBox.dataset.harga || 0);
        let unitCount = unitEl ? parseInt(unitEl.value || 1) : parseInt(calcBox.dataset.unit || 1);
        if (isNaN(unitCount) || unitCount < 1) unitCount = 1;

        if (elemUnit) elemUnit.textContent = unitCount + ' Unit';

        const extrabedCount = parseInt(calcBox.dataset.extrabedCount || 0);
        const extrabedHarga = parseFloat(calcBox.dataset.extrabedHarga || 150000);
        const diskonNilai = parseFloat(calcBox.dataset.diskon || 0);
        const totalLama = parseFloat(calcBox.dataset.totalLama || 0);

        let totalBaru = (diffDays * hargaPerMalam * unitCount);
        if (extrabedCount > 0) {
            totalBaru += (extrabedCount * extrabedHarga * diffDays);
        }
        totalBaru = Math.max(0, totalBaru - diskonNilai);

        const selisih = totalBaru - totalLama;

        if (elemMalam) elemMalam.textContent = diffDays + ' Malam';
        if (elemTotal) elemTotal.textContent = formatRupiah(totalBaru);
        if (elemSelisih) {
            if (selisih > 0) {
                elemSelisih.className = 'text-danger fw-bold';
                elemSelisih.textContent = '+' + formatRupiah(selisih) + ' (Penambahan Sisa Bayar)';
            } else if (selisih < 0) {
                elemSelisih.className = 'text-success fw-bold';
                elemSelisih.textContent = '-' + formatRupiah(Math.abs(selisih)) + ' (Pengurangan Sisa Bayar)';
            } else {
                elemSelisih.className = 'text-primary fw-bold';
                elemSelisih.textContent = 'Rp 0 (Harga Tetap)';
            }
        }
    }

    document.querySelectorAll('.checkin-reschedule, .checkout-reschedule, .unit-reschedule').forEach(function(input) {
        input.addEventListener('change', function() {
            const id = this.dataset.id;
            recalculate(id);
        });
        input.addEventListener('input', function() {
            const id = this.dataset.id;
            recalculate(id);
        });
    });

    // Handle Bayar Pelunasan via Midtrans
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-bayar-pelunasan');
        if (!btn) return;

        e.preventDefault();
        const id = btn.dataset.id;
        const originalText = btn.innerHTML;

        if (typeof window.snap === 'undefined' || !window.snap.pay) {
            alert('Sistem pembayaran Midtrans sedang dimuat. Silakan tunggu beberapa detik dan klik kembali.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

        // Tutup modal detail jika sedang terbuka agar popup Midtrans muncul di depan
        const openModal = btn.closest('.modal');
        let modalInstance = null;
        if (openModal && typeof bootstrap !== 'undefined') {
            modalInstance = bootstrap.Modal.getInstance(openModal);
            if (modalInstance) {
                modalInstance.hide();
            }
        }

        fetch('/pemesanan/' + id + '/snap-pelunasan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (data.snap_token) {
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Konfirmasi...';
                        fetch('/pemesanan/' + id + '/konfirmasi-pelunasan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(result)
                        })
                        .then(res => res.json())
                        .then(() => {
                            window.location.reload();
                        })
                        .catch(() => {
                            window.location.reload();
                        });
                    },
                    onPending: function(result) {
                        alert('Instruksi pembayaran telah dikirim. Silakan selesaikan pembayaran di aplikasi bank Anda.');
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal atau dibatalkan.');
                    },
                    onClose: function() {
                        // Dialog ditutup user
                    }
                });
            } else {
                alert(data.error || 'Gagal memproses pembayaran pelunasan.');
                if (modalInstance) modalInstance.show();
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Terjadi kesalahan koneksi saat memproses pelunasan.');
            if (modalInstance) modalInstance.show();
        });
    });

    // Handle Bayar Denda Kerusakan via Midtrans
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-bayar-denda');
        if (!btn) return;

        e.preventDefault();
        const id = btn.dataset.id;
        const originalText = btn.innerHTML;

        if (typeof window.snap === 'undefined' || !window.snap.pay) {
            alert('Sistem pembayaran Midtrans sedang dimuat. Silakan tunggu beberapa detik dan klik kembali.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses Denda...';

        const openModal = btn.closest('.modal');
        let modalInstance = null;
        if (openModal && typeof bootstrap !== 'undefined') {
            modalInstance = bootstrap.Modal.getInstance(openModal);
            if (modalInstance) {
                modalInstance.hide();
            }
        }

        fetch('/pemesanan/' + id + '/snap-denda', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (data.snap_token) {
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Konfirmasi...';
                        fetch('/pemesanan/' + id + '/konfirmasi-denda', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(result)
                        })
                        .then(res => res.json())
                        .then(() => { window.location.reload(); })
                        .catch(() => { window.location.reload(); });
                    },
                    onPending: function(result) {
                        alert('Instruksi pembayaran denda telah dikirim. Silakan selesaikan pembayaran di aplikasi bank Anda.');
                    },
                    onError: function(result) {
                        alert('Pembayaran denda gagal atau dibatalkan.');
                    },
                    onClose: function() {}
                });
            } else {
                alert(data.error || 'Gagal memproses pembayaran denda.');
                if (modalInstance) modalInstance.show();
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Terjadi kesalahan koneksi saat memproses pembayaran denda.');
            if (modalInstance) modalInstance.show();
        });
    });
});
</script>

{{-- Midtrans Snap JS SDK --}}
<script src="{{ config('midtrans.is_production', false) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key', env('MIDTRANS_CLIENT_KEY')) }}"></script>
@endsection
