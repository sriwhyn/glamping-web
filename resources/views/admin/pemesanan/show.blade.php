@extends('layouts.admin')

@section('title', 'Detail Pemesanan #' . $pemesanan->id_pemesanan)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Detail Pemesanan #{{ $pemesanan->id_pemesanan }}</h4>
        <p class="text-muted mb-0 small">Informasi lengkap transaksi reservasi pengunjung.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.pemesanan.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            Kembali
        </a>
        @php
            $isOnlinePayment = ($pemesanan->pembayaran && (!$pemesanan->pembayaran->bukti_pembayaran || $pemesanan->pembayaran->bukti_pembayaran == 'images/logo1.jpg'));
        @endphp
        @if(!$isOnlinePayment)
            <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalDelete">
                Hapus Pemesanan
            </button>
        @endif
    </div>
</div>

<div class="row g-4">
    {{-- Main Detail Card --}}
    <div class="col-lg-8">
        {{-- Card Information Pemesanan --}}
        <div class="card border-0 rounded-3 shadow-sm bg-white mb-4">
            <div class="card-header bg-light border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark">Data Reservasi</h6>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-0" style="width: 200px;">ID Pemesanan</td>
                            <td class="fw-bold text-dark">: #{{ $pemesanan->id_pemesanan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Nama Pengunjung</td>
                            <td class="fw-bold text-dark">: {{ $pemesanan->user->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Email</td>
                            <td class="fw-bold text-dark">: {{ $pemesanan->user->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Nomor Telepon</td>
                            <td class="fw-bold text-dark">: {{ $pemesanan->user->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Glamping Dipesan</td>
                            <td class="fw-bold text-dark">: {{ $pemesanan->glamping->nama_glamping ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Jumlah Tamu</td>
                            <td class="fw-bold text-dark">: {{ $pemesanan->jumlah_tamu }} Orang</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Tanggal Check-in</td>
                            <td class="fw-bold text-dark">: {{ date('d F Y', strtotime($pemesanan->tanggal_checkin)) }} ({{ $pemesanan->jam_checkin ?? '14:00 WIB' }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Tanggal Check-out</td>
                            <td class="fw-bold text-dark">: {{ date('d F Y', strtotime($pemesanan->tanggal_checkout)) }} (12:00 WIB)</td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted ps-0 pt-3">Total Halaman / Tagihan</td>
                            <td class="fw-bold text-success fs-5 pt-3">: Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Status Pemesanan</td>
                            <td>: 
                                @if($isOnlinePayment)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill">
                                        Dikonfirmasi (Online)
                                    </span>
                                @elseif($pemesanan->status == 'Dikonfirmasi' || $pemesanan->status == 'Selesai')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill">
                                        {{ $pemesanan->status }}
                                    </span>
                                @elseif($pemesanan->status == 'Dibatalkan')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill">
                                        {{ $pemesanan->status }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5 rounded-pill">
                                        {{ $pemesanan->status }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Card Information Pembayaran --}}
        <div class="card border-0 rounded-3 shadow-sm bg-white">
            <div class="card-header bg-light border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-credit-card me-1.5 text-success"></i>Data & Riwayat Pembayaran</h6>
            </div>
            <div class="card-body p-4">
                {{-- 1. Pembayaran DP Awal --}}
                @if($pemesanan->pembayaran)
                    <div class="mb-3 pb-3 border-bottom">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small fw-semibold mb-2">
                            1. Pembayaran DP (50%)
                        </span>
                        <table class="table table-borderless table-sm align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0" style="width: 180px;">Metode Pembayaran</td>
                                    <td class="fw-semibold text-dark">: {{ $pemesanan->pembayaran->metode_pembayaran ?? 'Pembayaran Online' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Jumlah Dibayar</td>
                                    <td class="fw-bold text-success">: Rp {{ number_format($pemesanan->pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Status Verifikasi</td>
                                    <td class="fw-semibold text-dark">: 
                                        @if($pemesanan->pembayaran->status_verifikasi == 'Diterima')
                                            <span class="badge bg-success text-white px-2 py-0.5 rounded-pill">Diterima / Terverifikasi</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill">{{ $pemesanan->pembayaran->status_verifikasi }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Tanggal Pembayaran</td>
                                    <td class="fw-semibold text-dark">: {{ date('d/m/Y', strtotime($pemesanan->pembayaran->tanggal_pembayaran)) }}</td>
                                </tr>
                                @if($pemesanan->pembayaran->bukti_pembayaran && $pemesanan->pembayaran->bukti_pembayaran != 'images/logo1.jpg')
                                    <tr>
                                        <td class="text-muted ps-0 pt-2" valign="top">Bukti Pembayaran</td>
                                        <td class="pt-2">: 
                                            <div class="mt-1">
                                                <img src="{{ asset($pemesanan->pembayaran->bukti_pembayaran) }}" class="img-fluid rounded-3 border" style="max-height: 200px;">
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-muted ps-0">Keterangan Bukti</td>
                                        <td class="fw-semibold text-dark">: Transaksi Online (Terverifikasi Otomatis System)</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- 2. Status Pelunasan Tagihan Sewa --}}
                <div class="mb-3 {{ $pemesanan->denda_kerusakan > 0 ? 'pb-3 border-bottom' : '' }}">
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill small fw-semibold mb-2">
                        2. Pelunasan Sisa Sewa
                    </span>
                    <table class="table table-borderless table-sm align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted ps-0" style="width: 180px;">Status Pelunasan</td>
                                <td class="fw-bold">: 
                                    @if($pemesanan->status_pelunasan == 'Lunas')
                                        <span class="text-success">Lunas (100%)</span>
                                    @else
                                        <span class="text-danger">Belum Lunas (Sisa Rp {{ number_format($pemesanan->sisa_bayar, 0, ',', '.') }})</span>
                                    @endif
                                </td>
                            </tr>
                            @if($pemesanan->status_pelunasan == 'Lunas')
                                <tr>
                                    <td class="text-muted ps-0">Metode Pelunasan</td>
                                    <td class="fw-semibold text-dark">: {{ $pemesanan->metode_pelunasan ?? 'Tunai (Cash)' }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- 3. Pembayaran Denda Kerusakan (Jika Ada) --}}
                @if($pemesanan->denda_kerusakan > 0)
                    <div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill small fw-semibold mb-2">
                            3. Pembayaran Denda Kerusakan
                        </span>
                        <table class="table table-borderless table-sm align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0" style="width: 180px;">Nominal Denda</td>
                                    <td class="fw-bold text-danger">: Rp {{ number_format($pemesanan->denda_kerusakan, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Catatan Barang</td>
                                    <td class="fw-semibold text-dark">: {{ $pemesanan->catatan_kerusakan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Status Pembayaran Denda</td>
                                    <td>: 
                                        @if(str_contains($pemesanan->catatan_kerusakan ?? '', 'Lunas') || $pemesanan->status_deposit == 'Dikembalikan')
                                            <span class="badge bg-success text-white px-2 py-0.5 rounded-pill">Lunas / Selesai Dibayar</span>
                                        @else
                                            <span class="badge bg-danger text-white px-2 py-0.5 rounded-pill">Menunggu Pembayaran</span>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $pembayaranDenda = $pemesanan->semuaPembayaran ? $pemesanan->semuaPembayaran->firstWhere(function($pay) {
                                        return str_contains($pay->metode_pembayaran, 'Denda');
                                    }) : null;
                                @endphp
                                @if($pembayaranDenda)
                                    <tr>
                                        <td class="text-muted ps-0">Waktu Bayar Online</td>
                                        <td class="fw-semibold text-dark">: {{ date('d/m/Y', strtotime($pembayaranDenda->tanggal_pembayaran)) }} (Transaksi Online Terverifikasi)</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Side Action Card --}}
    <div class="col-lg-4">

        {{-- Informational Alert Reschedule --}}
        @if(($pemesanan->reschedule_count ?? 0) > 0)
            <div class="card border-0 rounded-3 shadow-sm bg-white mb-4 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-primary text-white rounded-pill px-2.5 py-1" style="font-size:10px;">
                            <i class="bi bi-arrow-repeat me-1"></i>Pemesanan Reschedule
                        </span>
                        @if($pemesanan->reschedule_at)
                            <small class="text-muted" style="font-size:10px;">{{ date('d M Y H:i', strtotime($pemesanan->reschedule_at)) }}</small>
                        @endif
                    </div>
                    <small class="text-dark d-block fw-semibold mt-1">
                        Pengunjung telah memperbarui jadwal menginap (1/1x).
                    </small>
                    <small class="text-muted d-block mt-0.5" style="font-size:11px;">
                        Tanggal Check-in: <strong>{{ date('d/m/Y', strtotime($pemesanan->tanggal_checkin)) }}</strong> s/d <strong>{{ date('d/m/Y', strtotime($pemesanan->tanggal_checkout)) }}</strong>
                    </small>
                </div>
            </div>
        @endif

        {{-- Status Pemesanan & Pelunasan --}}
        <div class="card border-0 rounded-3 shadow-sm bg-white mb-4">
            <div class="card-header bg-light border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 me-1.5" style="color:#387478;"></i>Status & Pelunasan Tagihan</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.pemesanan.status', $pemesanan->id_pemesanan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Status Reservasi</label>
                        @if($isOnlinePayment)
                            <input type="text" class="form-control rounded-3 bg-light text-success fw-bold" value="Dikonfirmasi (Online Payment)" readonly>
                        @else
                            <select name="status" class="form-select rounded-3 py-2" {{ $pemesanan->status_pelunasan == 'Lunas' ? 'disabled' : '' }}>
                                <option value="Menunggu Pembayaran" {{ $pemesanan->status == 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                <option value="Menunggu Verifikasi" {{ $pemesanan->status == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="Dikonfirmasi" {{ $pemesanan->status == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                <option value="Selesai" {{ $pemesanan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Dibatalkan" {{ $pemesanan->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        @endif
                    </div>

                    @if($pemesanan->status_pelunasan == 'Lunas')
                        {{-- Tampilan Terkunci Saat Sudah Lunas --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Status Pelunasan</label>
                            <input type="text" class="form-control rounded-3 bg-light text-success fw-bold" value="Lunas (100% Terbayar)" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Metode Pelunasan</label>
                            <input type="text" class="form-control rounded-3 bg-light text-dark fw-semibold" value="{{ $pemesanan->metode_pelunasan ?? 'Tunai (Cash)' }}" readonly>
                        </div>

                        <div class="alert alert-success rounded-3 small p-2.5 mb-0 border-0" style="background:#DCFCE7;color:#166534;">
                            <i class="bi bi-lock-fill me-1"></i><strong>Terkunci:</strong> Tagihan telah lunas 100%. Tidak dapat diubah.
                        </div>
                    @else
                        {{-- Form Pelunasan Aktif --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Status Pelunasan Sisa Tagihan</label>
                            <select name="status_pelunasan" id="selectPelunasan" class="form-select rounded-3 py-2 fw-semibold text-dark">
                                <option value="Belum Lunas" selected>Belum Lunas (Sisa Rp {{ number_format($pemesanan->sisa_bayar, 0, ',', '.') }})</option>
                                <option value="Lunas">Lunas (Sudah Bayar 100%)</option>
                            </select>
                        </div>

                        <div class="mb-3" id="metodePelunasanWrapper" style="display:none;">
                            <label class="form-label small fw-semibold text-muted">Metode Pelunasan</label>
                            <select name="metode_pelunasan" class="form-select rounded-3 py-2">
                                <option value="Tunai (Cash)">Tunai (Cash)</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size:11px;">Pilih cara pembayaran pelunasan oleh pengunjung di resepsionis.</small>
                        </div>

                        <button type="submit" class="btn text-white w-100 rounded-pill py-2 fw-semibold shadow-xs" style="background:#387478;">
                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                        </button>
                    @endif
                </form>
            </div>
        </div>

        {{-- Jaminan KTP & Kerusakan Barang --}}
        <div class="card border-0 rounded-3 shadow-sm bg-white">
            <div class="card-header bg-light border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge me-1.5 text-primary"></i>Jaminan KTP & Kerusakan Barang</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.pemesanan.status', $pemesanan->id_pemesanan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="jenis_deposit" value="KTP">

                    @php
                        $depositDikembalikan = ($pemesanan->status_deposit == 'Dikembalikan');
                    @endphp

                    {{-- Langkah 1: Jaminan --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Jaminan Menginap</label>
                        <input type="text" class="form-control rounded-3 bg-light text-dark fw-semibold" value="KTP / Kartu Identitas Asli" readonly>
                    </div>

                    {{-- Langkah 2: Status KTP --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Status KTP Pengunjung</label>
                        @if($depositDikembalikan)
                            <input type="text" class="form-control rounded-3 bg-light text-success fw-bold" value="Dikembalikan (Check-out Selesai)" readonly>
                            <input type="hidden" name="status_deposit" value="Dikembalikan">
                        @else
                            <select name="status_deposit" id="selectStatusDeposit" class="form-select rounded-3 py-2">
                                <option value="Belum Diserahkan" {{ ($pemesanan->status_deposit ?? 'Belum Diserahkan') == 'Belum Diserahkan' ? 'selected' : '' }}>Belum Diserahkan (Belum Check-in)</option>
                                <option value="Ditahan" {{ $pemesanan->status_deposit == 'Ditahan' ? 'selected' : '' }}>Ditahan oleh Resepsionis (Sedang Menginap)</option>
                                <option value="Dikembalikan" {{ $pemesanan->status_deposit == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan ke Pengunjung (Check-out)</option>
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size:11px;">
                                Belum Diserahkan → Tamu check-in, serahkan KTP → Ditahan → Check-out aman → Dikembalikan.
                            </small>
                        @endif
                    </div>

                    @if(!$depositDikembalikan)
                        {{-- Toggle Kerusakan --}}
                        <div class="form-check form-switch mb-3 p-2 bg-light rounded-3 ps-5 border">
                            <input class="form-check-input" type="checkbox" role="switch" id="toggleKerusakan" {{ ($pemesanan->denda_kerusakan > 0 || !empty($pemesanan->catatan_kerusakan)) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-bold text-dark" for="toggleKerusakan">
                                Ada Kerusakan / Kehilangan Barang?
                            </label>
                            <small class="text-muted d-block" style="font-size:10.5px;">Aktifkan jika ada fasilitas yang rusak dan perlu ganti rugi.</small>
                        </div>

                        {{-- Form Denda --}}
                        <div id="kerusakanWrapper" style="{{ ($pemesanan->denda_kerusakan > 0 || !empty($pemesanan->catatan_kerusakan)) ? '' : 'display:none;' }}">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-danger">Denda Kerusakan Barang (Rp)</label>
                                <input type="number" name="denda_kerusakan" id="inputDendaKerusakan" class="form-control rounded-3 py-2 border-danger-subtle" placeholder="0" value="{{ (int) $pemesanan->denda_kerusakan }}" min="0" step="10000">
                                <small class="text-muted d-block mt-1" style="font-size:11px;">Nominal biaya penggantian kerusakan fasilitas.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">Catatan Kerusakan Barang</label>
                                <textarea name="catatan_kerusakan" id="inputCatatanKerusakan" class="form-control rounded-3" rows="3" placeholder="Contoh: Gelas pecah 2 pcs (Lunas Tunai)">{{ $pemesanan->catatan_kerusakan }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-semibold shadow-xs">
                            <i class="bi bi-shield-check me-1"></i>Simpan Status Jaminan
                        </button>
                    @else
                        {{-- Readonly info kerusakan jika sudah dikembalikan --}}
                        @if($pemesanan->denda_kerusakan > 0)
                            <div class="p-3 rounded-3 bg-light border mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Denda Kerusakan:</span>
                                    <strong class="text-danger">Rp {{ number_format($pemesanan->denda_kerusakan, 0, ',', '.') }}</strong>
                                </div>
                                @if($pemesanan->catatan_kerusakan)
                                    <div class="small text-muted mt-1 pt-1 border-top">
                                        <strong>Catatan:</strong> {{ $pemesanan->catatan_kerusakan }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="alert alert-success rounded-3 small p-2.5 mb-0 border-0" style="background:#DCFCE7;color:#166534;">
                            <i class="bi bi-lock-fill me-1"></i><strong>Terkunci:</strong> KTP sudah dikembalikan ke pengunjung. Status tidak dapat diubah.
                        </div>
                    @endif
                </form>
            </div>
        </div>

    </div>
</div>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle metode pelunasan
    const selectPelunasan = document.getElementById('selectPelunasan');
    const metodePelunasanWrapper = document.getElementById('metodePelunasanWrapper');
    if (selectPelunasan && metodePelunasanWrapper) {
        selectPelunasan.addEventListener('change', function() {
            metodePelunasanWrapper.style.display = this.value === 'Lunas' ? '' : 'none';
        });
    }

    // Toggle kerusakan
    const toggleKerusakan = document.getElementById('toggleKerusakan');
    const kerusakanWrapper = document.getElementById('kerusakanWrapper');
    const inputDendaKerusakan = document.getElementById('inputDendaKerusakan');
    const inputCatatanKerusakan = document.getElementById('inputCatatanKerusakan');

    if (toggleKerusakan && kerusakanWrapper) {
        toggleKerusakan.addEventListener('change', function() {
            if (this.checked) {
                kerusakanWrapper.style.display = '';
            } else {
                kerusakanWrapper.style.display = 'none';
                if (inputDendaKerusakan) inputDendaKerusakan.value = 0;
                if (inputCatatanKerusakan) inputCatatanKerusakan.value = '';
            }
        });
    }
});
</script>

{{-- Modal Hapus Pemesanan --}}
@if(!$isOnlinePayment)
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-3 border-0 shadow">
                <div class="modal-header border-bottom py-2.5 px-3">
                    <h6 class="modal-title fw-bold text-danger">Hapus Pemesanan?</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="small text-muted mb-3">Apakah Anda yakin ingin menghapus transaksi <strong>#{{ $pemesanan->id_pemesanan }}</strong> ini?</p>
                    <form action="{{ route('admin.pemesanan.destroy', $pemesanan->id_pemesanan) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 rounded-pill btn-sm fw-bold">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
