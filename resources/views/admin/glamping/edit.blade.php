@extends('layouts.admin')

@section('title', 'Edit Glamping')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Edit Unit Glamping</h3>
        <p class="text-muted mb-0">Ubah informasi unit glamping {{ $glamping->nama_glamping }}.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.glamping.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
    <div class="card-body p-4">
        <form action="{{ route('admin.glamping.update', $glamping->id_glamping) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Unit Glamping</label>
                    <input type="text" name="nama_glamping" class="form-control rounded-3 py-2" value="{{ old('nama_glamping', $glamping->nama_glamping) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipe Glamping</label>
                    <select name="id_tipe" class="form-select rounded-3 py-2" required>
                        <option value="">-- Pilih Tipe --</option>
                        @foreach($tipes as $t)
                            <option value="{{ $t->id_tipe }}" {{ old('id_tipe', $glamping->id_tipe) == $t->id_tipe ? 'selected' : '' }}>{{ $t->nama_tipe }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Harga per Malam (Rp)</label>
                    <input type="number" name="harga" class="form-control rounded-3 py-2" value="{{ old('harga', $glamping->harga) }}" required>
                </div>
                <div class="col-md-6" id="boxExtraBedAdmin">
                    <label class="form-label fw-semibold">Harga Extra Bed (Rp / Satuan)</label>
                    <input type="number" name="harga_extrabed" class="form-control rounded-3 py-2" value="{{ old('harga_extrabed', $glamping->harga_extrabed ?? 150000) }}">
                    <small class="text-muted">Hanya untuk tipe Glamping. Default Rp 150.000 per malam.</small>
                </div>

                {{-- Kapasitas --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kapasitas Dewasa (per Unit)</label>
                    <input type="number" name="kapasitas_dewasa" class="form-control rounded-3 py-2" value="{{ old('kapasitas_dewasa', $glamping->kapasitas_dewasa ?? 2) }}" min="1" required>
                    <small class="text-muted">Anak maks 1 orang (≤ 6 tahun) per unit.</small>
                </div>


                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jumlah Total Unit Kamar</label>
                    <input type="number" name="jumlah_unit" class="form-control rounded-3 py-2" value="{{ old('jumlah_unit', $glamping->jumlah_unit ?? 1) }}" min="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status Ketersediaan</label>
                    <select name="status" class="form-select rounded-3 py-2" required>
                        <option value="tersedia" {{ old('status', $glamping->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="tidak tersedia" {{ old('status', $glamping->status) == 'tidak tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>

                {{-- Upload Foto --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ganti Foto Utama Unit (Opsional)</label>
                    <input type="file" name="gambar" class="form-control rounded-3 py-2" accept="image/*">
                    <small class="text-danger fw-semibold d-block">Maksimal ukuran file: 2 MB (Format: JPG, PNG, WEBP)</small>
                    @if($glamping->gambar)
                        <div class="mt-2">
                            <img src="{{ asset($glamping->gambar) }}" 
                                 class="rounded-3 border cursor-pointer shadow-xs" 
                                 width="100" height="70" 
                                 style="object-fit:cover; object-position:center;" 
                                 data-bs-toggle="modal" 
                                 data-bs-target="#modalFotoEditSlide" 
                                 data-index="0"
                                 title="Klik untuk melihat foto utuh (dapat digeser)">
                            <small class="text-muted d-block mt-1">Foto utama saat ini (Klik untuk perbesar/geser)</small>
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tambah Foto Galeri Tambahan</label>
                    <input type="file" name="foto_tambahan[]" class="form-control rounded-3 py-2" accept="image/*" multiple>
                    <small class="text-danger fw-semibold d-block">Maksimal 2 MB per file. Pilih beberapa foto sekaligus.</small>
                </div>

                {{-- Galeri Foto --}}
                @if($glamping->galeri && $glamping->galeri->count() > 0)
                    <div class="col-12 mt-3">
                        <label class="form-label fw-semibold d-block">Foto Galeri Tambahan Saat Ini</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($glamping->galeri as $idx => $foto)
                                <div class="position-relative border rounded-3 overflow-hidden p-1 bg-light text-center" style="width:110px;">
                                    <img src="{{ asset($foto->foto) }}" 
                                         class="rounded-2 w-100 cursor-pointer" 
                                         style="height:75px; object-fit:cover; object-position:center;" 
                                         data-bs-toggle="modal" 
                                         data-bs-target="#modalFotoEditSlide" 
                                         data-index="{{ $idx + 1 }}"
                                         title="Klik untuk melihat foto utuh (dapat digeser)">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 py-0 mt-1" style="font-size:11px;" onclick="if(confirm('Hapus foto ini dari galeri?')) document.getElementById('form-hapus-foto-{{ $foto->id_galeri }}').submit();">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control rounded-3" rows="4" required>{{ old('deskripsi', $glamping->deskripsi) }}</textarea>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn text-white rounded-pill px-5 py-2" style="background:#387478;">
                        <i class="bi bi-check-lg me-1"></i>Perbarui Unit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Form Hapus Foto --}}
@if($glamping->galeri && $glamping->galeri->count() > 0)
    @foreach($glamping->galeri as $foto)
        <form id="form-hapus-foto-{{ $foto->id_galeri }}" action="{{ route('admin.glamping.hapusFoto', $foto->id_galeri) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif

{{-- Modal Lightbox Galeri --}}
<div class="modal fade" id="modalFotoEditSlide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-4 shadow bg-dark text-white">
            <div class="modal-header border-bottom border-secondary py-3">
                <h6 class="modal-title fw-bold text-white">
                    <i class="bi bi-images me-2 text-warning"></i>Galeri {{ $glamping->nama_glamping }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0 position-relative">
                <div id="carouselEditLightbox" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner py-4">
                        {{-- Foto Utama --}}
                        <div class="carousel-item active">
                            <img src="{{ asset($glamping->gambar ?? 'images/bg.jpeg') }}" class="img-fluid rounded-3 shadow" style="max-height:78vh; width:auto; max-width:100%; object-fit:contain;" alt="{{ $glamping->nama_glamping }}">
                            <div class="mt-2 text-center small text-light opacity-75">Foto Utama - {{ $glamping->nama_glamping }}</div>
                        </div>
                        {{-- Foto Galeri --}}
                        @if($glamping->galeri && $glamping->galeri->count() > 0)
                            @foreach($glamping->galeri as $idx => $foto)
                                <div class="carousel-item">
                                    <img src="{{ asset($foto->foto) }}" class="img-fluid rounded-3 shadow" style="max-height:78vh; width:auto; max-width:100%; object-fit:contain;" alt="Galeri {{ $glamping->nama_glamping }}">
                                    <div class="mt-2 text-center small text-light opacity-75">Foto Galeri {{ $idx + 1 }} dari {{ $glamping->galeri->count() }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @if($glamping->galeri && $glamping->galeri->count() > 0)
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselEditLightbox" data-bs-slide="prev" style="width:8%;">
                            <span class="carousel-control-prev-icon p-3 rounded-circle bg-dark bg-opacity-75 border" aria-hidden="true"></span>
                            <span class="visually-hidden">Sebelumnya</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselEditLightbox" data-bs-slide="next" style="width:8%;">
                            <span class="carousel-control-next-icon p-3 rounded-circle bg-dark bg-opacity-75 border" aria-hidden="true"></span>
                            <span class="visually-hidden">Selanjutnya</span>
                        </button>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-top border-secondary py-2 justify-content-between">
                <small class="text-light opacity-75"><i class="bi bi-arrows-left-right me-1"></i>Gunakan panah di samping atau geser layar untuk berpindah foto</small>
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // Tampilkan kolom Extra Bed HANYA jika tipe yang dipilih mengandung kata 'glamping'
    var tipeSelect = document.querySelector('select[name="id_tipe"]');
    var boxEB = document.getElementById('boxExtraBedAdmin');
    function cekTipeExtraBed() {
        if (!tipeSelect || !boxEB) return;
        var nama = tipeSelect.options[tipeSelect.selectedIndex].text.toLowerCase();
        if (nama.indexOf('glamping') >= 0) {
            boxEB.style.display = '';     // Tampilkan untuk Glamping
        } else {
            boxEB.style.display = 'none'; // Sembunyikan untuk Cabin, Family Room, dll.
        }
    }
    if (tipeSelect) {
        tipeSelect.addEventListener('change', cekTipeExtraBed);
        cekTipeExtraBed();
    }

    // Modal Lightbox Slide Event Listener
    document.addEventListener('DOMContentLoaded', function() {
        const modalFotoEdit = document.getElementById('modalFotoEditSlide');
        if (modalFotoEdit) {
            modalFotoEdit.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const slideIndex = button ? (button.getAttribute('data-index') || 0) : 0;
                const carouselEl = document.getElementById('carouselEditLightbox');
                if (carouselEl && window.bootstrap) {
                    const carouselInstance = bootstrap.Carousel.getOrCreateInstance(carouselEl);
                    carouselInstance.to(parseInt(slideIndex) || 0);
                }
            });
        }
    });
</script>
@endsection

