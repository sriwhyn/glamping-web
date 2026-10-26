@extends('layouts.admin')

@section('title', 'Kelola Fasilitas Layanan')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Fasilitas Layanan Glamping</h3>
        <p class="text-muted mb-0">Kelola daftar fasilitas khusus yang tersedia di setiap unit glamping.</p>
    </div>
    <div class="col-auto">
        <button class="btn text-white rounded-pill px-4" style="background:#387478;" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle me-1"></i>Tambah Fasilitas
        </button>
    </div>
</div>

<div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Foto</th>
                        <th>Unit Glamping</th>
                        <th>Nama Fasilitas</th>
                        <th>Deskripsi</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fasilitas as $index => $f)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                @if($f->gambar)
                                    <img src="{{ asset($f->gambar) }}" class="rounded-3 shadow-sm border" width="50" height="50" style="object-fit:cover;">
                                @else
                                    <div class="rounded-3 bg-secondary-subtle d-flex align-items-center justify-content-center border" style="width:50px;height:50px;">
                                        <i class="bi bi-image text-muted fs-4"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-bold" style="color:#243642;">{{ $f->glamping->nama_glamping ?? 'Semua Unit' }}</td>
                            <td><span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">{{ $f->nama }}</span></td>
                            <td>{{ $f->deskripsi ?? '-' }}</td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-outline-dark rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $f->id_fasilitas }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.fasilitas.destroy', $f->id_fasilitas) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus fasilitas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="modalEdit{{ $f->id_fasilitas }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="{{ route('admin.fasilitas.update', $f->id_fasilitas) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom py-3">
                                            <h5 class="modal-title fw-bold" style="color:#243642;">Edit Fasilitas</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Pilih Unit Glamping</label>
                                                <select name="id_glamping" class="form-select rounded-3" required>
                                                    @foreach($glampings as $g)
                                                        <option value="{{ $g->id_glamping }}" {{ $f->id_glamping == $g->id_glamping ? 'selected' : '' }}>{{ $g->nama_glamping }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Fasilitas</label>
                                                <input type="text" name="nama" class="form-control rounded-3" value="{{ $f->nama }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Foto Fasilitas (Opsional)</label>
                                                @if($f->gambar)
                                                    <div class="mb-2">
                                                        <img src="{{ asset($f->gambar) }}" class="rounded-3 border" width="70" height="70" style="object-fit:cover;">
                                                        <small class="text-muted d-block mt-1">Foto saat ini</small>
                                                    </div>
                                                @endif
                                                <input type="file" name="gambar" class="form-control rounded-3" accept="image/*">
                                                <small class="text-danger fw-semibold d-block mt-1">Maksimal ukuran foto: 2 MB (JPG, PNG, WEBP).</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control rounded-3" rows="3">{{ $f->deskripsi }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top py-3">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn text-white rounded-pill px-4" style="background:#387478;">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada fasilitas layanan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('admin.fasilitas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" style="color:#243642;">Tambah Fasilitas Layanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Unit Glamping</label>
                        <select name="id_glamping" class="form-select rounded-3" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($glampings as $g)
                                <option value="{{ $g->id_glamping }}">{{ $g->nama_glamping }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Fasilitas</label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: WiFi Cepat, Kamar Mandi Air Hangat" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Fasilitas (Opsional)</label>
                        <input type="file" name="gambar" class="form-control rounded-3" accept="image/*">
                        <small class="text-danger fw-semibold d-block mt-1">Maksimal ukuran foto: 2 MB (JPG, PNG, WEBP).</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="3" placeholder="Keterangan singkat fasilitas"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background:#387478;">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
