@extends('layouts.user')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 text-center">

            <div class="card border rounded-3 p-4 p-md-5 mb-4 bg-white shadow-sm">
                <div class="mb-4">
                    <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center p-3" style="width:80px;height:80px;">
                        <i class="bi bi-check-lg display-4 text-success fw-bold"></i>
                    </div>
                </div>

                @if($pemesanan->status == 'Dikonfirmasi')
                    <h3 class="fw-bold text-dark mb-2">Pembayaran Berhasil</h3>
                    <p class="text-muted mb-4">Reservasi unit glamping Anda telah resmi dikonfirmasi.</p>
                @else
                    <h3 class="fw-bold text-dark mb-2">Bukti Pembayaran Terkirim</h3>
                    <p class="text-muted mb-4">Bukti pembayaran Anda sedang diverifikasi oleh tim kami. Status reservasi akan segera diperbarui.</p>
                @endif

                {{-- Ringkasan transaksi --}}
                <div class="p-4 rounded-4 border text-start bg-light mb-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div>
                            <small class="text-muted d-block">ID Reservasi</small>
                            <strong class="fw-bold text-dark fs-5">#{{ $pemesanan->id_pemesanan }}</strong>
                        </div>
                        <div>
                            @if($pemesanan->status == 'Dikonfirmasi')
                                <span class="badge bg-success px-3 py-1.5 rounded-pill">
                                    Dikonfirmasi
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill">
                                    Menunggu Verifikasi
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($pemesanan->glamping)
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ asset($pemesanan->glamping->gambar ?? 'images/bg.jpeg') }}" class="rounded-3 me-3 flex-shrink-0" width="60" height="60" style="object-fit:cover;">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">{{ $pemesanan->glamping->nama_glamping }}</h6>
                                <small class="text-muted d-block">{{ $pemesanan->jumlah_tamu }} Tamu</small>
                            </div>
                        </div>
                    @endif

                    <div class="row g-3 small border-top pt-3">
                        <div class="col-6">
                            <span class="text-muted d-block">Tanggal Check-in:</span>
                            <strong class="text-dark">{{ date('d M Y', strtotime($pemesanan->tanggal_checkin)) }} ({{ $pemesanan->jam_checkin ?? '14:00' }})</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Tanggal Check-out:</span>
                            <strong class="text-dark">{{ date('d M Y', strtotime($pemesanan->tanggal_checkout)) }} (12:00)</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Total Tagihan:</span>
                            <strong class="text-dark">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Jumlah Pembayaran:</span>
                            <strong class="text-success fs-6">Rp {{ number_format($pemesanan->pembayaran->jumlah_bayar ?? ($pemesanan->total_harga * 0.5), 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Tombol navigasi --}}
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="{{ route('riwayat.index') }}" class="btn text-white rounded-pill px-4 py-2.5 fw-semibold shadow-xs" style="background:#387478;">
                        Lihat Riwayat Pesanan
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold">
                        Kembali ke Beranda
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
