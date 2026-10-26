@extends('layouts.user')

@section('title', 'Galeri')

@section('content')
<div class="py-5" style="background:#243642;">
    <div class="container text-center text-white">
        <span class="badge px-3 py-2 mb-3" style="background:#629584;"><i class="bi bi-images me-1"></i>Galeri Foto</span>
        <h1 class="fw-bold mb-3">Galeri Danau Kembar Scenery</h1>
        <p class="lead text-light opacity-75 mx-auto" style="max-width:700px;">Dokumentasi keindahan alam, fasilitas glamping, dan keseruan momen para pengunjung selama menginap.</p>
    </div>
</div>

<div class="container py-5">
    @if(isset($galeris) && count($galeris) > 0)
        <div class="row g-4 mb-5">
            @foreach($galeris as $idx => $g)
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 position-relative group">
                        <img src="{{ asset($g->foto) }}" 
                             onerror="this.onerror=null;this.src='{{ asset('images/bg.jpeg') }}';"
                             class="card-img-top w-100 cursor-pointer" 
                             style="height:280px;object-fit:cover;object-position:center;transition:transform 0.3s;" 
                             alt="{{ $g->keterangan ?? 'Galeri Glamping' }}"
                             data-bs-toggle="modal" 
                             data-bs-target="#modalGaleriSlide" 
                             data-index="{{ $idx }}"
                             title="Klik untuk lihat foto utuh (dapat digeser)">
                        <div class="card-body bg-white">
                            <span class="badge px-2 py-1 mb-2" style="background:#E2F1E7;color:#243642;">
                                <i class="bi bi-house-door me-1"></i>{{ $g->glamping->nama_glamping ?? 'Glamping Danau Kembar' }}
                            </span>
                            <h6 class="fw-bold text-dark mb-0">{{ $g->keterangan ?? 'Keindahan Alam Danau Kembar' }}</h6>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Modal Slide Galeri --}}
        <div class="modal fade" id="modalGaleriSlide" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 rounded-4 shadow bg-dark text-white">
                    <div class="modal-header border-bottom border-secondary py-3">
                        <h6 class="modal-title fw-bold text-white">
                            <i class="bi bi-images me-2 text-success"></i>Galeri Danau Kembar Scenery
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-0 position-relative">
                        <div id="carouselGaleriLightbox" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner py-4">
                                @foreach($galeris as $idx => $g)
                                    <div class="carousel-item {{ $idx == 0 ? 'active' : '' }}">
                                        <img src="{{ asset($g->foto) }}" onerror="this.onerror=null;this.src='{{ asset('images/bg.jpeg') }}';" class="img-fluid rounded-3 shadow" style="max-height:78vh; width:auto; max-width:100%; object-fit:contain;" alt="{{ $g->keterangan ?? 'Galeri' }}">
                                        <div class="mt-2 text-center text-light">
                                            <span class="badge bg-success mb-1">{{ $g->glamping->nama_glamping ?? 'Glamping Danau Kembar' }}</span>
                                            <p class="mb-0 small opacity-90">{{ $g->keterangan ?? 'Dokumentasi Suasana Glamping' }} ({{ $idx + 1 }} dari {{ count($galeris) }})</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(count($galeris) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselGaleriLightbox" data-bs-slide="prev" style="width:8%;">
                                    <span class="carousel-control-prev-icon p-3 rounded-circle bg-dark bg-opacity-75 border" aria-hidden="true"></span>
                                    <span class="visually-hidden">Sebelumnya</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselGaleriLightbox" data-bs-slide="next" style="width:8%;">
                                    <span class="carousel-control-next-icon p-3 rounded-circle bg-dark bg-opacity-75 border" aria-hidden="true"></span>
                                    <span class="visually-hidden">Selanjutnya</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary py-2 justify-content-between">
                        <small class="text-light opacity-75"><i class="bi bi-arrows-left-right me-1"></i>Gunakan panah di samping atau geser layar untuk berpindah foto galeri</small>
                        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="text-center py-5 mb-5 bg-white rounded-4 border p-4">
            <p class="text-muted mb-2">Saat ini belum ada foto galeri yang diunggah.</p>
            <a href="{{ route('glamping.index') }}" class="btn text-white px-4 py-2 rounded-pill fw-semibold" style="background:#387478;">Lihat Unit Glamping</a>
        </div>
    @endif

    <div class="text-center p-5 rounded-4 text-white" style="background:#243642;">
        <h3 class="fw-bold mb-3">Tertarik Merasakan Sendiri Suasananya?</h3>
        <p class="text-light opacity-75 mb-4">Pesan unit glamping favorit Anda dan ciptakan kenangan berharga di Danau Kembar.</p>
        <a href="{{ route('glamping.index') }}" class="btn text-white rounded-pill px-5 py-3 fw-bold" style="background:#387478;">
            <i class="bi bi-calendar-check me-2"></i>Pesan Glamping Sekarang
        </a>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalGaleri = document.getElementById('modalGaleriSlide');
        if (modalGaleri) {
            modalGaleri.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const slideIndex = button ? (button.getAttribute('data-index') || 0) : 0;
                const carouselEl = document.getElementById('carouselGaleriLightbox');
                if (carouselEl && window.bootstrap) {
                    const carouselInstance = bootstrap.Carousel.getOrCreateInstance(carouselEl);
                    carouselInstance.to(parseInt(slideIndex) || 0);
                }
            });
        }
    });
</script>
@endsection

