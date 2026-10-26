@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1" style="color:#243642;">Verifikasi Pembayaran</h3>
    <p class="text-muted mb-0">Kelola dan verifikasi transaksi pembayaran dari pengunjung glamping.</p>
</div>

<!-- {{-- Ringkasan Status Pembayaran --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white border-start border-4 border-primary">
            <span class="text-muted small fw-semibold d-block">Total Transaksi</span>
            <h4 class="fw-bold mb-0 mt-1" style="color:#243642;">{{ $totalCount ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white border-start border-4 border-warning">
            <span class="text-muted small fw-semibold d-block">Perlu Verifikasi</span>
            <h4 class="fw-bold mb-0 mt-1 text-warning">{{ $menungguCount ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white border-start border-4 border-success">
            <span class="text-muted small fw-semibold d-block">Disetujui</span>
            <h4 class="fw-bold mb-0 mt-1 text-success">{{ $diterimaCount ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white border-start border-4 border-danger">
            <span class="text-muted small fw-semibold d-block">Ditolak</span>
            <h4 class="fw-bold mb-0 mt-1 text-danger">{{ $ditolakCount ?? 0 }}</h4>
        </div>
    </div>
</div> -->

{{-- Form Filter & Search --}}
<div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
    <div class="card-body p-3">
        <form action="{{ route('admin.pembayaran.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari ID Pemesanan, nama pengunjung, email..." value="{{ request('search') }}">
            </div>
            <div class="col-7 col-md-4">
                <select name="status_verifikasi" class="form-select bg-light border-0">
                    <option value="">Semua Status Verifikasi</option>
                    <option value="Menunggu" {{ request('status_verifikasi') == 'Menunggu' ? 'selected' : '' }}>Perlu Verifikasi (Menunggu)</option>
                    <option value="Diterima" {{ request('status_verifikasi') == 'Diterima' ? 'selected' : '' }}>Disetujui / Diterima</option>
                    <option value="Ditolak" {{ request('status_verifikasi') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-5 col-md-2 d-flex gap-2">
                <button type="submit" class="btn text-white rounded-3 w-100 fw-semibold" style="background:#387478;">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status_verifikasi']))
                    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-light border rounded-3" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Tabel Pembayaran --}}
<div class="card border-0 rounded-3 shadow-sm bg-white overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3 text-secondary small text-uppercase" style="width:120px;">ID Pemesanan</th>
                        <th class="py-3 text-secondary small text-uppercase">Pengunjung</th>
                        <th class="py-3 text-secondary small text-uppercase">Metode Bayar</th>
                        <th class="py-3 text-secondary small text-uppercase">Jumlah Bayar</th>
                        <th class="py-3 text-secondary small text-uppercase text-center">Bukti Pembayaran</th>
                        <th class="py-3 text-secondary small text-uppercase text-center">Status Verifikasi</th>
                        <th class="text-center pe-4 py-3 text-secondary small text-uppercase" style="width:190px;">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($pembayarans as $p)
                        @php
                            $isAutoPayment = (!$p->bukti_pembayaran || $p->bukti_pembayaran == 'images/logo1.jpg');
                            $isPending = (!in_array($p->status_verifikasi, ['Diterima', 'Disetujui', 'Ditolak']));
                        @endphp
                        <tr>
                            {{-- ID Pemesanan --}}
                            <td class="ps-4">
                                <a href="{{ route('admin.pemesanan.show', $p->id_pemesanan) }}" class="fw-bold text-decoration-none" style="color:#387478;">
                                    #{{ $p->id_pemesanan }}
                                </a>
                            </td>

                            {{-- Pengunjung --}}
                            <td>
                                <div class="fw-semibold text-dark">{{ $p->pemesanan->user->nama ?? '-' }}</div>
                                <small class="text-muted d-block text-truncate" style="max-width: 180px;">{{ $p->pemesanan->user->email ?? '-' }}</small>
                            </td>

                            {{-- Metode Bayar --}}
                            <td>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-3 fw-medium">
                                    {{ $p->metode_pembayaran ?? 'Pembayaran Online' }}
                                </span>
                            </td>

                            {{-- Jumlah Bayar --}}
                            <td>
                                <span class="fw-bold text-dark fs-6" style="color:#243642;">
                                    Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Bukti Pembayaran --}}
                            <td class="text-center">
                                @if(!$isAutoPayment)
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-medium" data-bs-toggle="modal" data-bs-target="#modalBukti{{ $p->id_pembayaran }}">
                                        Lihat Bukti
                                    </button>

                                    {{-- Modal Lightbox Bukti Transfer --}}
                                    <div class="modal fade" id="modalBukti{{ $p->id_pembayaran }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-md">
                                            <div class="modal-content rounded-3 border-0 shadow">
                                                <div class="modal-header border-bottom py-3 px-4">
                                                    <h6 class="modal-title fw-bold text-dark mb-0">
                                                        Bukti Transfer #{{ $p->id_pemesanan }}
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-center bg-light">
                                                    <img src="{{ asset($p->bukti_pembayaran) }}" class="img-fluid rounded-3 shadow-sm border mb-3" style="max-height: 420px; object-fit: contain;">
                                                    <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 border">
                                                        <div class="text-start">
                                                            <small class="text-muted d-block">Pengunjung: <strong>{{ $p->pemesanan->user->nama ?? '-' }}</strong></small>
                                                            <small class="text-muted d-block">Nominal: <strong class="text-success">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</strong></small>
                                                        </div>
                                                        <a href="{{ asset($p->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill">
                                                            Ukuran Penuh
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-normal" style="font-size: 11px;">
                                        Pembayaran Online
                                    </span>
                                @endif
                            </td>

                            {{-- Status Verifikasi --}}
                            <td class="text-center">
                                @if($p->status_verifikasi == 'Diterima' || $p->status_verifikasi == 'Disetujui')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill">
                                        Disetujui
                                    </span>
                                @elseif($p->status_verifikasi == 'Ditolak')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5 rounded-pill">
                                        Menunggu Verifikasi
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi Verifikasi --}}
                            <td class="text-center pe-4">
                                @if($isAutoPayment)
                                    {{-- Pembayaran Otomatis Online: Tidak memerlukan aksi verifikasi manual --}}
                                    <span class="badge bg-light text-muted border px-3 py-1.5 rounded-pill fw-normal" style="font-size: 11px;">
                                        Otomatis Terverifikasi
                                    </span>
                                @elseif($isPending)
                                    {{-- Pembayaran Manual yang Masih Menunggu Verifikasi --}}
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <form action="{{ route('admin.pembayaran.verifikasi', $p->id_pembayaran) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status_verifikasi" value="Diterima">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 text-nowrap fw-medium" title="Setujui Pembayaran">
                                                Terima
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.pembayaran.verifikasi', $p->id_pembayaran) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status_verifikasi" value="Ditolak">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 text-nowrap fw-medium" title="Tolak Pembayaran">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @elseif($p->status_verifikasi == 'Diterima' || $p->status_verifikasi == 'Disetujui')
                                    <span class="text-success small fw-semibold">
                                        Selesai (Disetujui)
                                    </span>
                                @else
                                    <span class="text-danger small fw-semibold">
                                        Selesai (Ditolak)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada data pembayaran yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
