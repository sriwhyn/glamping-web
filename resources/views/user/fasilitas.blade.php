@extends('layouts.user')

@section('title', 'Fasilitas')

@section('content')
<div class="py-5" style="background:#243642;">
    <div class="container text-center text-white">
        <h1 class="fw-bold mb-3">Fasilitas Glamping Danau Kembar</h1>
        <p class="lead text-light opacity-75 mx-auto" style="max-width:700px;">Berikut fasilitas yang tersedia di area glamping kami.</p>
    </div>
</div>

{{-- Fasilitas Unit --}}
<div class="container py-5">
    @if($fasilitas->count() > 0)
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color:#243642;">Fasilitas Per Unit Glamping</h2>
            <p class="text-muted mx-auto" style="max-width:600px;">Daftar fasilitas yang tersedia untuk masing-masing unit glamping. Klik foto untuk melihat gambar lebih besar.</p>
        </div>

        <div class="row g-4">
            @foreach($fasilitas as $namaGlamping => $items)
                <div class="col-lg-6">
                    <div class="card border shadow-sm rounded-3 h-100 overflow-hidden">
                        <div class="card-header py-3 px-4 border-0 text-white d-flex justify-content-between align-items-center" style="background:#243642;">
                            <h5 class="fw-bold mb-0">{{ $namaGlamping }}</h5>
                            <span class="badge bg-light text-dark rounded-pill px-3">{{ count($items) }} Fasilitas</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                @foreach($items as $item)
                                    <div class="col-sm-6">
                                        <div class="p-3 rounded-3 border h-100 bg-light">
                                            <div class="d-flex align-items-start">
                                                @if($item->gambar)
                                                    <img src="{{ asset($item->gambar) }}" 
                                                         class="rounded-3 me-3 flex-shrink-0 border cursor-pointer facility-img-clickable" 
                                                         width="60" height="60" 
                                                         style="object-fit:cover; cursor:pointer;" 
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#modalFotoFasilitas" 
                                                         data-img="{{ asset($item->gambar) }}" 
                                                         data-title="{{ $item->nama }}" 
                                                         data-desc="{{ $item->deskripsi ?? '' }}"
                                                         title="Klik untuk memperbesar">
                                                @endif
                                                <div>
                                                    <h6 class="fw-bold mb-1 small text-dark">{{ $item->nama }}</h6>
                                                    @if($item->deskripsi)
                                                        <p class="text-muted small mb-0 lh-sm" style="font-size:0.8rem;">{{ $item->deskripsi }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <h4 class="fw-bold text-muted mb-3">Belum Ada Data Fasilitas</h4>
            <p class="text-muted">Data fasilitas belum tersedia saat ini.</p>
            <a href="{{ route('glamping.index') }}" class="btn text-white px-4 py-2 fw-semibold" style="background:#387478;">Lihat Unit Glamping</a>
        </div>
    @endif
</div>

{{-- Modal Foto Fasilitas --}}
<div class="modal fade" id="modalFotoFasilitas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark" id="modalFotoTitle">Detail Foto Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-dark">
                <img id="modalFotoImg" src="" class="img-fluid w-100" style="max-height:500px; object-fit:contain;">
            </div>
            <div class="modal-footer border-top py-3 justify-content-start bg-light">
                <div>
                    <h6 class="fw-bold mb-1 text-dark" id="modalFotoNama"></h6>
                    <p class="text-muted small mb-0" id="modalFotoDesc"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalFoto = document.getElementById('modalFotoFasilitas');
        if (modalFoto) {
            modalFoto.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imgSrc = button.getAttribute('data-img');
                const title = button.getAttribute('data-title');
                const desc = button.getAttribute('data-desc');

                document.getElementById('modalFotoImg').src = imgSrc;
                document.getElementById('modalFotoTitle').innerText = title;
                document.getElementById('modalFotoNama').innerText = title;
                document.getElementById('modalFotoDesc').innerText = desc || 'Tidak ada deskripsi tambahan.';
            });
        }
    });
</script>
@endsection
