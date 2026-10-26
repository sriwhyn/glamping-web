@extends('layouts.user')

@section('title', 'Beranda - Danau Kembar Scenery')

@section('content')

{{-- Hero Banner --}}
<section class="position-relative py-5 text-white" style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.65)), url('{{ asset('images/login.jpeg') }}') center/cover no-repeat; min-height: 420px;">
    <div class="container py-5">
        <div class="row align-items-center py-3">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3" style="background:#387478!important; font-size: 0.85rem;">
                    Glamping Danau Kembar Scenery
                </span>
                <h1 class="display-4 fw-bold mb-3">Nikmati Suasana Alam Danau Kembar</h1>
                <p class="lead mb-4 text-light opacity-90">Penginapan alam terbuka di kawasan Danau Kembar, Solok, Sumatera Barat dengan hawa sejuk dan pemandangan danau.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#glamping" class="btn text-white btn-lg px-4 py-3 fw-bold rounded-pill shadow-sm" style="background:#387478;">
                        Lihat Kategori Glamping
                    </a>
                    <a href="{{ route('glamping.index') }}" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold rounded-pill">
                        Cek Ketersediaan Kamar
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Form Pencarian --}}
<div class="container" style="margin-top: -35px; position: relative; z-index: 10;">
    <form action="{{ route('glamping.index') }}" method="GET" class="card border-0 shadow rounded-4 p-4 bg-white">
        <div class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold small text-muted">Tanggal Check-in</label>
                <input type="date" name="tanggal_checkin" class="form-control rounded-3 py-2" value="{{ request('tanggal_checkin') }}" min="{{ date('Y-m-d') }}">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold small text-muted">Tanggal Check-out</label>
                <input type="date" name="tanggal_checkout" class="form-control rounded-3 py-2" value="{{ request('tanggal_checkout') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold small text-muted">Jumlah Tamu</label>
                <input type="number" name="kapasitas" class="form-control rounded-3 py-2" value="{{ request('kapasitas') }}" min="1" placeholder="Jumlah orang">
            </div>
            <div class="col-lg-3 col-md-6 d-grid">
                <button type="submit" class="btn text-white py-2 rounded-3 fw-bold" style="background:#387478;">
                    Cari Ketersediaan
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Keunggulan --}}
<section class="py-5 bg-white mt-2">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color:#243642;">Fasilitas & Suasana</h3>
            <p class="text-muted col-lg-7 mx-auto">Suasana menginap nyaman di tepi Danau Kembar.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-light">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3 text-white" style="width:60px; height:60px; background:#387478;">
                        <i class="bi bi-cloud-sun fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">Pemandangan & Udara Sejuk</h6>
                    <p class="text-muted small mb-0">Pemandangan Danau Kembar dengan hawa sejuk pegunungan.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-light">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3 text-white" style="width:60px; height:60px; background:#629584;">
                        <i class="bi bi-house-heart fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">Kamar Mandi Dalam</h6>
                    <p class="text-muted small mb-0">Dilengkapi tempat tidur dan kamar mandi dalam yang bersih.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-light">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3 text-white" style="width:60px; height:60px; background:#387478;">
                        <i class="bi bi-fire fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">Area Api Unggun</h6>
                    <p class="text-muted small mb-0">Area outdoor untuk bersantai dan api unggun saat malam hari.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-light">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3 text-white" style="width:60px; height:60px; background:#629584;">
                        <i class="bi bi-calendar-check fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">Pemesanan Langsung</h6>
                    <p class="text-muted small mb-0">Cek jadwal ketersediaan kamar dan pesan secara online.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Unit Glamping --}}
<section id="glamping" class="py-5" style="background:#F8FAF8;">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color:#243642;">Pilihan Unit Glamping</h3>
            <p class="text-muted">Daftar unit penginapan yang tersedia.</p>
        </div>

        <div class="row g-4">
            @forelse($glampings as $g)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="position-relative overflow-hidden">
                            <img src="{{ asset($g->gambar ?? 'images/bg.jpeg') }}" 
                                 class="card-img-top cursor-pointer" 
                                 style="height:230px; object-fit:cover; object-position:center;" 
                                 alt="{{ $g->nama_glamping }}"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#modalPreviewFotoHome" 
                                 data-img="{{ asset($g->gambar ?? 'images/bg.jpeg') }}" 
                                 data-title="{{ $g->nama_glamping }}"
                                 title="Klik untuk melihat foto utuh">
                            
                            @if($g->is_full_today)
                                <span class="position-absolute top-0 start-0 m-3 badge px-3 py-2" style="background:#95A5A6;">
                                    Hari Ini Penuh
                                </span>
                            @else
                                <span class="position-absolute top-0 start-0 m-3 badge bg-success px-3 py-2">
                                    Tersedia (Sisa {{ $g->sisa_unit }} Unit)
                                </span>
                            @endif

                            <span class="position-absolute top-0 end-0 m-3 badge bg-dark px-3 py-2">
                                {{ $g->tipeGlamping->nama_tipe ?? 'Kategori' }}
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="fw-bold mb-0 text-dark">{{ $g->nama_glamping }}</h5>
                                @if(isset($g->has_diskon) && $g->has_diskon)
                                    <span class="badge bg-danger">DISKON</span>
                                @endif
                            </div>

                            <p class="text-muted small flex-grow-1 mb-3">
                                {{ Str::limit($g->deskripsi, 90) }}
                            </p>

                            @php
                                $nTipe = strtolower($g->tipeGlamping->nama_tipe ?? '');
                                $nUnit = strtolower($g->nama_glamping);
                                $isFam = str_contains($nTipe, 'family') || str_contains($nUnit, 'family');
                            @endphp
                            <div class="mb-3 small text-secondary">
                                @if($isFam)
                                    Kapasitas: <strong>Maks {{ $g->kapasitas ?? 5 }} Orang (Bebas Kombinasi)</strong> &middot; Tersedia: <strong>{{ $g->sisa_unit }} Unit</strong>
                                @else
                                    Kapasitas: <strong>{{ $g->kapasitas }} Orang</strong> &middot; Tersedia: <strong>{{ $g->sisa_unit }} Unit</strong>
                                @endif
                            </div>

                            @if($g->is_full_today && $g->tanggal_tersedia)
                                <div class="mb-3 p-2 rounded-2 small" style="background:#E2F1E7; color:#243642; border:1px solid #629584;">
                                    Tersedia mulai <strong>{{ date('d M Y', strtotime($g->tanggal_tersedia)) }}</strong>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div>
                                    <small class="text-muted d-block" style="font-size:0.75rem;">Harga Per Malam</small>
                                    @if(isset($g->has_diskon) && $g->has_diskon)
                                        <div>
                                            <span class="text-muted text-decoration-line-through me-1 small">Rp {{ number_format($g->harga_asli, 0, ',', '.') }}</span>
                                            <span class="fw-bold text-success fs-5">Rp {{ number_format($g->harga_diskon, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <h5 class="mb-0 fw-bold text-success">Rp {{ number_format($g->harga, 0, ',', '.') }} <small class="text-muted fs-6 fw-normal">/malam</small></h5>
                                    @endif
                                </div>
                                @if($g->is_full_today)
                                    <a href="{{ route('glamping.show', $g->id_glamping) }}" class="btn btn-outline-dark rounded-pill px-3 fw-semibold">Cek Tanggal Lain</a>
                                @else
                                    <a href="{{ route('glamping.show', $g->id_glamping) }}" class="btn text-white rounded-pill px-4 fw-semibold" style="background:#387478;">Detail & Pesan</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted mb-0">Belum ada unit glamping tersedia.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('glamping.index') }}" class="btn btn-outline-dark px-4 py-2 rounded-pill fw-semibold">
                Lihat Semua Unit Glamping
            </a>
        </div>
    </div>
</section>

{{-- Galeri Foto --}}
<section id="galeri-beranda" class="py-5 bg-white">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color:#243642;">Galeri Foto</h3>
                <p class="text-muted mb-0">Dokumentasi suasana penginapan dan lingkungan Danau Kembar.</p>
            </div>
            <a href="{{ route('galeri') }}" class="btn btn-outline-dark rounded-pill px-4 py-2 mt-2 mt-md-0 fw-semibold">
                Lihat Selengkapnya di Galeri <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($galeris as $idx => $g)
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                        <img src="{{ asset($g->foto) }}" 
                             class="card-img-top w-100 cursor-pointer" 
                             style="height:260px; object-fit:cover; object-position:center;" 
                             alt="{{ $g->keterangan ?? 'Galeri Resor' }}"
                             data-bs-toggle="modal" 
                             data-bs-target="#modalHomeGaleriSlide" 
                             data-index="{{ $idx }}"
                             title="Klik untuk melihat foto utuh">
                        <div class="card-body bg-white p-3">
                            <span class="badge px-2 py-1 mb-1" style="background:#E2F1E7; color:#243642; font-size: 11px;">
                                {{ $g->glamping->nama_glamping ?? 'Danau Kembar' }}
                            </span>
                            <h6 class="fw-bold text-dark mb-0 small">{{ $g->keterangan ?? 'Galeri suasana glamping' }}</h6>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">
                    <p class="mb-0">Belum ada foto galeri.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- Voucher Diskon --}}
@php
    $listDiskon = $diskons ?? $promos;
@endphp
@if($listDiskon && count($listDiskon) > 0)
<section id="diskon" class="py-5 text-white" style="background:#243642;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <h3 class="fw-bold mb-2 text-white">Voucher Diskon</h3>
                <p class="text-light opacity-75 mb-4">Gunakan kode diskon berikut saat melakukan reservasi unit.</p>
                <a href="{{ route('glamping.index') }}" class="btn btn-light px-4 py-2 rounded-pill fw-bold text-dark">
                    Pesan & Gunakan Diskon
                </a>
            </div>
            <div class="col-lg-7">
                <div class="row g-3">
                    @foreach($listDiskon as $d)
                        <div class="col-md-6">
                            <div class="card border-0 rounded-4 p-4 bg-white text-dark shadow-sm">
                                @if($d->jenis_diskon == 'persen')
                                    <span class="badge bg-danger w-auto mb-2 px-3 py-1 rounded-pill">Diskon {{ number_format($d->nilai_diskon, 0) }}%</span>
                                @else
                                    <span class="badge bg-danger w-auto mb-2 px-3 py-1 rounded-pill">Diskon Rp {{ number_format($d->nilai_diskon, 0, ',', '.') }}</span>
                                @endif
                                <h6 class="fw-bold mb-1">Voucher Diskon</h6>
                                <p class="text-muted small mb-0">Kode Diskon: <strong class="text-uppercase text-success">{{ $d->kode_diskon ?? $d->kode_promo }}</strong></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- {{-- Fasilitas --}}
@if($fasilitas && count($fasilitas) > 0)
<section id="fasilitas" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color:#243642;">Fasilitas Penginapan</h3>
            <p class="text-muted">Fasilitas pendukung yang tersedia di lokasi.</p>
        </div>

        <div class="row g-4">
            @foreach($fasilitas as $f)
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        @if($f->gambar)
                            <img src="{{ asset($f->gambar) }}" class="card-img-top" style="height:160px; object-fit:cover;" alt="{{ $f->nama }}">
                        @else
                            <div class="card-img-top bg-success text-white d-flex align-items-center justify-content-center" style="height:160px;">
                                <i class="bi bi-stars fs-1"></i>
                            </div>
                        @endif
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1 text-dark">{{ $f->nama }}</h6>
                            <p class="card-text text-muted small mb-0">{{ Str::limit($f->deskripsi ?? 'Fasilitas pendukung.', 75) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif -->

{{-- Ulasan Pengunjung --}}
<section id="galeri-testimoni" class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color:#243642;">Ulasan Pengunjung</h3>
            <p class="text-muted">Ulasan dari pengunjung yang pernah menginap.</p>
        </div>

        <div class="row g-4">
            @forelse($reviews as $r)
                <div class="col-lg-4 col-md-6">
                    <div class="card border shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="mb-2 text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $r->rating ? '-fill' : '' }}"></i>
                            @endfor
                        </div>
                        <p class="text-muted fst-italic mb-3 small lh-base">"{{ $r->komentar }}"</p>
                        <div class="mt-auto d-flex align-items-center justify-content-between pt-3 border-top">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark small">{{ $r->user->nama ?? 'Pengunjung' }}</h6>
                                <small class="text-muted">{{ $r->glamping->nama_glamping ?? 'Glamping' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">
                    <p class="mb-0">Belum ada ulasan dari pengunjung.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- Modal Foto --}}
<div class="modal fade" id="modalPreviewFotoHome" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark" id="modalFotoTitleHome">Foto Unit Glamping</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4 bg-dark rounded-bottom-4">
                <img id="modalFotoImgHome" src="" class="img-fluid rounded-3 shadow" style="max-height:78vh; max-width:100%; object-fit:contain;">
            </div>
        </div>
    </div>
</div>

{{-- Modal Galeri Slide --}}
@if(isset($galeris) && count($galeris) > 0)
<div class="modal fade" id="modalHomeGaleriSlide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-4 shadow bg-dark text-white">
            <div class="modal-header border-bottom border-secondary py-3">
                <h6 class="modal-title fw-bold text-white">
                    Galeri Resor Danau Kembar Scenery
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0 position-relative">
                <div id="carouselHomeGaleriLightbox" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner py-4">
                        @foreach($galeris as $idx => $g)
                            <div class="carousel-item {{ $idx == 0 ? 'active' : '' }}">
                                <img src="{{ asset($g->foto) }}" class="img-fluid rounded-3 shadow" style="max-height:78vh; width:auto; max-width:100%; object-fit:contain;" alt="{{ $g->keterangan ?? 'Galeri' }}">
                                <div class="mt-2 text-center text-light">
                                    <span class="badge bg-success mb-1">{{ $g->glamping->nama_glamping ?? 'Danau Kembar' }}</span>
                                    <p class="mb-0 small opacity-90">{{ $g->keterangan ?? 'Dokumentasi Suasana Glamping' }} ({{ $idx + 1 }} dari {{ count($galeris) }})</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if(count($galeris) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselHomeGaleriLightbox" data-bs-slide="prev" style="width:8%;">
                            <span class="carousel-control-prev-icon p-3 rounded-circle bg-dark bg-opacity-75 border" aria-hidden="true"></span>
                            <span class="visually-hidden">Sebelumnya</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselHomeGaleriLightbox" data-bs-slide="next" style="width:8%;">
                            <span class="carousel-control-next-icon p-3 rounded-circle bg-dark bg-opacity-75 border" aria-hidden="true"></span>
                            <span class="visually-hidden">Selanjutnya</span>
                        </button>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-top border-secondary py-2 justify-content-between">
                <small class="text-light opacity-75">Gunakan panah di samping atau geser layar untuk berpindah foto galeri</small>
                <a href="{{ route('galeri') }}" class="btn btn-sm btn-success rounded-pill px-4 me-2">Lihat Semua Galeri</a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalSingle = document.getElementById('modalPreviewFotoHome');
        if (modalSingle) {
            modalSingle.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imgSrc = button.getAttribute('data-img');
                const title = button.getAttribute('data-title');
                document.getElementById('modalFotoImgHome').src = imgSrc;
                document.getElementById('modalFotoTitleHome').innerText = title + ' - Foto Utuh';
            });
        }

        const modalGaleriHome = document.getElementById('modalHomeGaleriSlide');
        if (modalGaleriHome) {
            modalGaleriHome.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const slideIndex = button ? (button.getAttribute('data-index') || 0) : 0;
                const carouselEl = document.getElementById('carouselHomeGaleriLightbox');
                if (carouselEl && window.bootstrap) {
                    const carouselInstance = bootstrap.Carousel.getOrCreateInstance(carouselEl);
                    carouselInstance.to(parseInt(slideIndex) || 0);
                }
            });
        }
    });
</script>
@endsection
