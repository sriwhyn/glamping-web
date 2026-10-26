@extends('layouts.user')

@section('title', 'Pilihan Kategori Glamping')

@section('content')

{{-- Header --}}
<div class="container py-4">
    <div class="text-center mb-4">
        <span class="badge bg-success px-3 py-2 mb-2">Glamping Danau Kembar</span>
        <h2 class="fw-bold">Pilih Kategori Glamping</h2>
        <p class="text-muted col-lg-8 mx-auto">Kategori penginapan alam terbuka dengan fasilitas lengkap dan pemandangan Danau Kembar.</p>
    </div>

    {{-- Filter Ketersediaan --}}
    <div class="card border shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3 text-dark">Cek Ketersediaan per Tanggal</h6>
            <form action="{{ route('glamping.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold small text-muted">Tanggal Check-in</label>
                    <input type="date" name="tanggal_checkin" class="form-control py-2" value="{{ request('tanggal_checkin') }}" min="{{ date('Y-m-d') }}">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold small text-muted">Tanggal Check-out</label>
                    <input type="date" name="tanggal_checkout" class="form-control py-2" value="{{ request('tanggal_checkout') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold small text-muted">Tipe Glamping</label>
                    <select name="tipe" class="form-select py-2">
                        <option value="">Semua Tipe</option>
                        @foreach($tipes as $t)
                            <option value="{{ $t->id_tipe }}" {{ request('tipe') == $t->id_tipe ? 'selected' : '' }}>{{ $t->nama_tipe }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <button type="submit" class="btn text-white w-100 py-2 fw-semibold" style="background:#387478;">Cari Ketersediaan</button>
                </div>
            </form>
        </div>
    </div>

    @if($hasDateFilter)
        <div class="alert alert-success rounded-3 p-3 mb-4 d-flex align-items-center justify-content-between">
            <span>Ketersediaan unit untuk tanggal: <strong>{{ date('d M Y', strtotime($checkin)) }}</strong> s/d <strong>{{ date('d M Y', strtotime($checkout)) }}</strong></span>
            <a href="{{ route('glamping.index') }}" class="btn btn-sm btn-outline-dark">Reset Filter</a>
        </div>
    @endif

    {{-- Katalog Glamping --}}
    <div class="row g-4">
        @forelse($glampings as $g)
            <div class="col-lg-4 col-md-6">
                <div class="card border shadow-sm rounded-3 h-100 overflow-hidden bg-white">
                    <div class="position-relative overflow-hidden group">
                        <img src="{{ asset($g->gambar ?? 'images/bg.jpeg') }}" 
                             class="card-img-top cursor-pointer" 
                             style="height:230px; object-fit:cover; object-position:center; transition: transform 0.3s;" 
                             alt="{{ $g->nama_glamping }}"
                             data-bs-toggle="modal" 
                             data-bs-target="#modalPreviewFotoUser" 
                             data-img="{{ asset($g->gambar ?? 'images/bg.jpeg') }}" 
                             data-title="{{ $g->nama_glamping }}"
                             title="Klik untuk lihat foto utuh">
                        
                        {{-- Badge ketersediaan --}}
                        @if($hasDateFilter)
                            @if($g->is_full)
                                <span class="position-absolute top-0 start-0 m-3 badge px-3 py-2" style="background:#95A5A6;">
                                    Penuh untuk Tanggal Ini
                                </span>
                            @elseif($g->sisa_unit <= 1)
                                <span class="position-absolute top-0 start-0 m-3 badge px-3 py-2" style="background:#629584;">
                                    Sisa {{ $g->sisa_unit }} Unit Lagi!
                                </span>
                            @else
                                <span class="position-absolute top-0 start-0 m-3 badge bg-success px-3 py-2">
                                    Tersedia (Sisa {{ $g->sisa_unit }} Unit)
                                </span>
                            @endif
                        @endif

                        <span class="position-absolute top-0 end-0 m-3 badge bg-dark px-3 py-2">
                            {{ $g->tipeGlamping->nama_tipe ?? 'Kategori' }}
                        </span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="fw-bold mb-0 text-dark">{{ $g->nama_glamping }}</h5>
                            @if(isset($g->has_diskon) && $g->has_diskon)
                                <span class="badge bg-danger">DISKON Rp {{ number_format($g->potongan_harga, 0, ',', '.') }}</span>
                            @endif
                        </div>

                        <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($g->deskripsi, 95) }}</p>

                        @php
                            $nTipe = strtolower($g->tipeGlamping->nama_tipe ?? '');
                            $nUnit = strtolower($g->nama_glamping);
                            $isFam = str_contains($nTipe, 'family') || str_contains($nUnit, 'family');
                        @endphp
                        <div class="mb-2 small text-secondary">
                            Kapasitas per unit: 
                            @if($isFam)
                                <strong>Maksimal {{ $g->kapasitas ?? 5 }} Orang (Bebas Kombinasi Dewasa & Anak)</strong>
                            @else
                                <strong>{{ $g->kapasitas_dewasa ?? 2 }} Dewasa + {{ $g->kapasitas_anak ?? 1 }} Anak (≤ {{ $g->batas_umur_anak ?? 6 }}thn)</strong>
                            @endif
                        </div>
                        <div class="mb-3 small text-secondary">
                            Total persediaan: <strong>{{ $g->jumlah_unit ?? 1 }} Unit</strong>
                        </div>

                        {{-- Info Ketersediaan --}}
                        @if($hasDateFilter && $g->is_full && $g->tanggal_tersedia)
                            <div class="mb-3 p-2 rounded-2 small" style="background:#E2F1E7; color:#243642; border:1px solid #629584;">
                                Tersedia mulai <strong>{{ date('d M Y', strtotime($g->tanggal_tersedia)) }}</strong>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top">
                            <div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Tarif Per Malam / Unit</small>
                                @if(isset($g->has_diskon) && $g->has_diskon)
                                    <div>
                                        <span class="text-muted text-decoration-line-through me-1 small">Rp {{ number_format($g->harga_asli, 0, ',', '.') }}</span>
                                        <span class="fw-bold text-success fs-5">Rp {{ number_format($g->harga_diskon, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <h5 class="mb-0 fw-bold text-success">Rp {{ number_format($g->harga, 0, ',', '.') }} <small class="text-muted fs-6 fw-normal">/malam</small></h5>
                                @endif
                            </div>
                        </div>

                        {{-- Tombol --}}
                        @if($hasDateFilter && $g->is_full)
                            <a href="{{ route('glamping.show', $g->id_glamping) }}" class="btn btn-outline-dark w-100 py-2 fw-semibold">Cek Kalender & Tanggal Lain</a>
                        @else
                            <a href="{{ route('glamping.show', $g->id_glamping) }}" class="btn text-white w-100 py-2 fw-semibold" style="background:#387478;">Lihat Detail & Pesan</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted mb-0">Tidak ada kategori glamping yang sesuai filter.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal Foto Unit --}}
<div class="modal fade" id="modalPreviewFotoUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark" id="modalFotoTitleUser">Foto Unit Glamping</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4 bg-dark rounded-bottom-4">
                <img id="modalFotoImgUser" src="" class="img-fluid rounded-3 shadow" style="max-height:78vh; max-width:100%; object-fit:contain;">
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalPreviewFotoUser');
        if (modal) {
            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imgSrc = button.getAttribute('data-img');
                const title = button.getAttribute('data-title');
                document.getElementById('modalFotoImgUser').src = imgSrc;
                document.getElementById('modalFotoTitleUser').innerText = title + ' - Foto Utuh (Penuh)';
            });
        }
    });
</script>
@endsection

