@extends('layouts.admin')

@section('title', 'Daftar Pemesanan')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1" style="color:#243642;">Kelola Pemesanan</h3>
    <p class="text-muted mb-0">Daftar seluruh transaksi pemesanan glamping dari pengunjung.</p>
</div>

<!-- {{-- Ringkasan Status Pemesanan --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white border-start border-4 border-primary">
            <span class="text-muted small fw-semibold d-block">Total Pemesanan</span>
            <h4 class="fw-bold mb-0 mt-1" style="color:#243642;">{{ $totalCount ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white border-start border-4 border-info">
            <span class="text-muted small fw-semibold d-block">Perlu Verifikasi</span>
            <h4 class="fw-bold mb-0 mt-1 text-info">{{ $menungguVerifikasiCount ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white border-start border-4 border-success">
            <span class="text-muted small fw-semibold d-block">Dikonfirmasi / Selesai</span>
            <h4 class="fw-bold mb-0 mt-1 text-success">{{ ($dikonfirmasiCount ?? 0) + ($selesaiCount ?? 0) }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white border-start border-4 border-danger">
            <span class="text-muted small fw-semibold d-block">Dibatalkan</span>
            <h4 class="fw-bold mb-0 mt-1 text-danger">{{ $dibatalkanCount ?? 0 }}</h4>
        </div>
    </div>
</div> -->

{{-- Form Filter & Search --}}
<div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
    <div class="card-body p-3">
        <form action="{{ route('admin.pemesanan.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari ID, nama, email, glamping..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select bg-light border-0">
                    <option value="">Semua Status Pemesanan</option>
                    <option value="Menunggu Pembayaran" {{ request('status') == 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="Menunggu Verifikasi" {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Dikonfirmasi" {{ request('status') == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <input type="date" name="tanggal_checkin" class="form-control bg-light border-0" value="{{ request('tanggal_checkin') }}" title="Filter Tanggal Check-in">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn text-white rounded-3 w-100 fw-semibold" style="background:#387478;">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'tanggal_checkin']))
                    <a href="{{ route('admin.pemesanan.index') }}" class="btn btn-light border rounded-3" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Tabel Pemesanan --}}
<div class="card border-0 rounded-3 shadow-sm bg-white overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3 text-secondary small text-uppercase" style="width:110px;">ID</th>
                        <th class="py-3 text-secondary small text-uppercase">Pengunjung</th>
                        <th class="py-3 text-secondary small text-uppercase">Glamping</th>
                        <th class="py-3 text-secondary small text-uppercase">Check-in / Check-out</th>
                        <th class="py-3 text-secondary small text-uppercase">Total & Pelunasan</th>
                        <th class="py-3 text-secondary small text-uppercase">Status & Deposit</th>
                        <th class="text-center pe-4 py-3 text-secondary small text-uppercase" style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($pemesanans as $p)
                        @php
                            $isOnlinePayment = ($p->pembayaran && (!$p->pembayaran->bukti_pembayaran || $p->pembayaran->bukti_pembayaran == 'images/logo1.jpg'));
                        @endphp
                        <tr>
                            {{-- ID Pemesanan --}}
                            <td class="ps-4">
                                <a href="{{ route('admin.pemesanan.show', $p->id_pemesanan) }}" class="fw-bold text-decoration-none" style="color:#387478;">
                                    #{{ $p->id_pemesanan }}
                                </a>
                                @if(($p->reschedule_count ?? 0) > 0)
                                    <div class="mt-1">
                                        @if(($p->is_reschedule_notified ?? 1) == 0)
                                            <span class="badge bg-danger text-white rounded-pill px-2 py-0.5" style="font-size:9.5px;"><i class="bi bi-bell-fill me-1"></i>Reschedule Baru</span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5" style="font-size:9.5px;"><i class="bi bi-arrow-repeat me-1"></i>Reschedule (1x)</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            {{-- Pengunjung --}}
                            <td>
                                <div class="fw-semibold text-dark">{{ $p->user->nama ?? '-' }}</div>
                                <small class="text-muted d-block text-truncate" style="max-width: 180px;">{{ $p->user->email ?? '-' }}</small>
                            </td>

                            {{-- Glamping --}}
                            <td>
                                <span class="fw-semibold text-dark d-block text-truncate" style="max-width:200px;">
                                    {{ $p->glamping->nama_glamping ?? '-' }}
                                </span>
                            </td>

                            {{-- Check-in / Check-out --}}
                            <td class="text-nowrap">
                                <span class="fw-semibold text-dark d-block">
                                    {{ date('d M Y', strtotime($p->tanggal_checkin)) }}
                                    <small class="text-muted">({{ $p->jam_checkin ?? '14:00 WIB' }})</small>
                                </span>
                                <small class="text-muted">s/d {{ date('d M Y', strtotime($p->tanggal_checkout)) }} (12:00 WIB)</small>
                            </td>

                            {{-- Total Harga & Pelunasan --}}
                            <td>
                                <span class="fw-bold text-dark d-block fs-6" style="color:#243642;">
                                    Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                                </span>
                                @if($p->status_pelunasan == 'Lunas')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill" style="font-size:10px;"><i class="bi bi-check-all me-1"></i>Lunas</span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-0.5 rounded-pill" style="font-size:10px;"><i class="bi bi-clock me-1"></i>Sisa: Rp {{ number_format($p->sisa_bayar, 0, ',', '.') }}</span>
                                @endif
                            </td>

                            {{-- Status Pemesanan, KTP & Denda --}}
                            <td>
                                <div class="mb-1">
                                    @if($isOnlinePayment)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                                            Dikonfirmasi (Online)
                                        </span>
                                    @elseif($p->status == 'Dikonfirmasi' || $p->status == 'Selesai')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                                            {{ $p->status }}
                                        </span>
                                    @elseif($p->status == 'Dibatalkan')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill">
                                            {{ $p->status }}
                                        </span>
                                    @elseif($p->status == 'Menunggu Verifikasi')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill">
                                            {{ $p->status }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill">
                                            {{ $p->status }}
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex flex-column gap-1 mt-1">
                                    {{-- Status KTP --}}
                                    @if($p->status_deposit == 'Ditahan')
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-0.5 rounded-pill" style="font-size:9.5px; width:fit-content;">
                                            <i class="bi bi-person-badge me-1"></i>KTP: Ditahan
                                        </span>
                                    @elseif($p->status_deposit == 'Dikembalikan')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill" style="font-size:9.5px; width:fit-content;">
                                            <i class="bi bi-check-circle me-1"></i>KTP: Dikembalikan
                                        </span>
                                    @elseif($p->status_deposit == 'Belum Diserahkan')
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5 rounded-pill" style="font-size:9.5px; width:fit-content;">
                                            <i class="bi bi-person-badge me-1"></i>KTP: Belum Diserahkan
                                        </span>
                                    @endif

                                    {{-- Status Denda Kerusakan --}}
                                    @if($p->denda_kerusakan > 0)
                                        @if(str_contains($p->catatan_kerusakan ?? '', 'Lunas') || $p->status_deposit == 'Dikembalikan')
                                            <span class="badge bg-success text-white px-2 py-0.5 rounded-pill" style="font-size:9.5px; width:fit-content;">
                                                <i class="bi bi-tools me-1"></i>Denda Rp {{ number_format($p->denda_kerusakan, 0, ',', '.') }} (Lunas)
                                            </span>
                                        @else
                                            <span class="badge bg-danger text-white px-2 py-0.5 rounded-pill" style="font-size:9.5px; width:fit-content;">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Denda Rp {{ number_format($p->denda_kerusakan, 0, ',', '.') }} (Belum Bayar)
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <a href="{{ route('admin.pemesanan.show', $p->id_pemesanan) }}" class="btn btn-sm btn-outline-dark rounded-pill px-2.5 py-1" title="Lihat Detail">
                                        Lihat
                                    </a>
                                    @if(!$isOnlinePayment)
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $p->id_pemesanan }}" title="Hapus Pemesanan">
                                            Hapus
                                        </button>
                                    @else
                                        <span class="text-muted small">Terkunci</span>
                                    @endif
                                </div>

                                {{-- Modal Hapus Pemesanan --}}
                                @if(!$isOnlinePayment)
                                    <div class="modal fade" id="modalDelete{{ $p->id_pemesanan }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content rounded-3 border-0 shadow">
                                                <div class="modal-header border-bottom py-2.5 px-3">
                                                    <h6 class="modal-title fw-bold text-danger">Hapus Pemesanan?</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-3 text-start">
                                                    <p class="small text-muted mb-3">Apakah Anda yakin ingin menghapus data pemesanan <strong>#{{ $p->id_pemesanan }}</strong> ({{ $p->user->nama ?? '-' }})?</p>
                                                    <form action="{{ route('admin.pemesanan.destroy', $p->id_pemesanan) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger w-100 rounded-pill btn-sm fw-bold">Ya, Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada data pemesanan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
