@extends('layouts.admin')

@section('title', 'Tambah Glamping')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Tambah Unit Glamping</h3>
        <p class="text-muted mb-0">Isi formulir untuk menambahkan unit glamping baru.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.glamping.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
    <div class="card-body p-4">
        <form action="{{ route('admin.glamping.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Unit Glamping</label>
                    <input type="text" name="nama_glamping" class="form-control rounded-3 py-2" placeholder="Contoh: Cabin Lake View A" value="{{ old('nama_glamping') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipe Glamping</label>
                    <select name="id_tipe" class="form-select rounded-3 py-2" required>
                        <option value="">-- Pilih Tipe --</option>
                        @foreach($tipes as $t)
                            <option value="{{ $t->id_tipe }}" {{ old('id_tipe') == $t->id_tipe ? 'selected' : '' }}>{{ $t->nama_tipe }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Harga per Malam (Rp)</label>
                    <input type="number" name="harga" class="form-control rounded-3 py-2" placeholder="Contoh: 750000" value="{{ old('harga') }}" required>
                </div>
                <div class="col-md-6" id="boxExtraBedAdmin">
                    <label class="form-label fw-semibold">Harga Extra Bed (Rp / Satuan)</label>
                    <input type="number" name="harga_extrabed" class="form-control rounded-3 py-2" placeholder="Contoh: 150000" value="{{ old('harga_extrabed', 150000) }}">
                    <small class="text-muted">Hanya untuk tipe Glamping. Default Rp 150.000 per malam.</small>
                </div>

                {{-- Kapasitas --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kapasitas Dewasa (per Unit)</label>
                    <input type="number" name="kapasitas_dewasa" class="form-control rounded-3 py-2" placeholder="Contoh: 2" value="{{ old('kapasitas_dewasa', 2) }}" min="1" required>
                    <small class="text-muted">Anak maks 1 orang (≤ 6 tahun) per unit.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jumlah Total Unit Kamar</label>
                    <input type="number" name="jumlah_unit" class="form-control rounded-3 py-2" placeholder="Contoh: 6" value="{{ old('jumlah_unit', 6) }}" min="1" required>
                    <small class="text-muted">Jumlah unit fisik yang tersedia untuk tipe ini.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status Ketersediaan</label>
                    <select name="status" class="form-select rounded-3 py-2" required>
                        <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="tidak tersedia" {{ old('status') == 'tidak tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>

                {{-- Upload Foto --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Foto Utama Unit</label>
                    <input type="file" name="gambar" class="form-control rounded-3 py-2" accept="image/*">
                    <small class="text-danger fw-semibold">Maksimal ukuran file: 2 MB (Format: JPG, PNG, WEBP)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Foto Galeri Tambahan (Bisa Pilih Beberapa)</label>
                    <input type="file" name="foto_tambahan[]" class="form-control rounded-3 py-2" accept="image/*" multiple>
                    <small class="text-danger fw-semibold">Maksimal 2 MB per file. Pilih beberapa foto sekaligus.</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control rounded-3" rows="4" placeholder="Jelaskan fasilitas dan keunggulan unit ini..." required>{{ old('deskripsi') }}</textarea>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn text-white rounded-pill px-5 py-2" style="background:#387478;">
                        <i class="bi bi-check-lg me-1"></i>Simpan Unit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    // Tampilkan kolom Extra Bed ]
    var tipeSelect = document.querySelector('select[name="id_tipe"]');
    var boxEB = document.getElementById('boxExtraBedAdmin');
    function cekTipeExtraBed() {
        if (!tipeSelect || !boxEB) return;
        var nama = tipeSelect.options[tipeSelect.selectedIndex].text.toLowerCase();
        if (nama.indexOf('glamping') >= 0) {
            boxEB.style.display = '';     // Tampilkan untuk Glamping
        } else {
            boxEB.style.display = 'none'; // Sembunyikan untuk Cabin, Family Room
        }
    }
    if (tipeSelect) {
        tipeSelect.addEventListener('change', cekTipeExtraBed);
        cekTipeExtraBed();
    }
</script>
@endsection
