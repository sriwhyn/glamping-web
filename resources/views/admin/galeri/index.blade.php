@extends('layouts.admin')

@section('title', 'Galeri')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Kelola Galeri</h3>
        <p class="text-muted mb-0">Daftar semua foto galeri yang terdaftar dalam sistem.</p>
    </div>
    <div class="col-auto">
        <button type="button" class="btn text-white rounded-pill px-4 shadow-sm" style="background:#387478;" data-bs-toggle="modal" data-bs-target="#modalTambahGaleri">
            <i class="bi bi-plus-lg me-1"></i>Tambah Foto
        </button>
    </div>
</div>

{{-- Modal Tambah Galeri --}}
<div class="modal fade" id="modalTambahGaleri" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold" style="color:#243642;"><i class="bi bi-image me-2 text-success"></i>Upload Foto Galeri Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih File Foto <span class="text-danger">*</span></label>
                        <input type="file" name="foto" class="form-control rounded-3" accept="image/*" required>
                        <small class="text-muted" style="font-size:11px;">Format: JPG, PNG, WEBP. Maksimal 3MB.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kaitkan dengan Unit Glamping (Opsional)</label>
                        <select name="id_glamping" class="form-select rounded-3">
                            <option value="">-- Tidak Terkait / Foto Umum --</option>
                            @foreach($glampings as $g)
                                <option value="{{ $g->id_glamping }}">{{ $g->nama_glamping }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan / Caption Foto</label>
                        <!-- <input type="text" name="keterangan" class="form-control rounded-3" placeholder="Contoh: Suasana matahari terbit di tepi danau"> -->
                        <input type="text" name="keterangan" class="form-control rounded-3" >
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background:#387478;">Upload Foto</button>
                </div>
            </form>
        </div>
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
                        <th>Kategori / Unit Glamping</th>
                        <th>Keterangan / Caption</th>
                        <th>Tanggal Upload</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($galeris as $index => $g)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ asset($g->foto) }}" width="70" height="50" class="rounded-3 shadow-xs" style="object-fit:cover;">
                            </td>
                            <td>
                                @if($g->glamping)
                                    <span class="badge" style="background:#629584;color:#fff;">{{ $g->glamping->nama_glamping }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1 rounded-pill">Foto Umum / Area Glamping</span>
                                @endif
                            </td>
                            <td class="fw-bold" style="color:#243642;">{{ $g->keterangan ?? '-' }}</td>
                            <td class="small text-muted">{{ $g->created_at ? $g->created_at->format('d M Y, H:i') : '-' }} WIB</td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $g->id_galeri }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $g->id_galeri }}" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>

                                {{-- Modal Edit Galeri --}}
                                <div class="modal fade" id="modalEdit{{ $g->id_galeri }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow text-start">
                                            <div class="modal-header border-bottom py-3 px-4">
                                                <h5 class="modal-title fw-bold" style="color:#243642;"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Foto Galeri</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.galeri.update', $g->id_galeri) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="mb-3 text-center">
                                                        <img src="{{ asset($g->foto) }}" class="rounded-3 shadow-xs mb-2" style="max-height:140px;object-fit:cover;">
                                                        <small class="d-block text-muted">Foto saat ini</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Ganti Foto (Biarkan kosong jika tidak diganti)</label>
                                                        <input type="file" name="foto" class="form-control rounded-3" accept="image/*">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Kaitkan dengan Unit Glamping</label>
                                                        <select name="id_glamping" class="form-select rounded-3">
                                                            <option value="">-- Tidak Terkait / Foto Umum --</option>
                                                            @foreach($glampings as $gl)
                                                                <option value="{{ $gl->id_glamping }}" {{ $g->id_glamping == $gl->id_glamping ? 'selected' : '' }}>{{ $gl->nama_glamping }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Keterangan / Caption Foto</label>
                                                        <input type="text" name="keterangan" class="form-control rounded-3" value="{{ $g->keterangan }}">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top py-2 px-4">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background:#387478;">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Hapus Galeri --}}
                                <div class="modal fade" id="modalDelete{{ $g->id_galeri }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content rounded-4 border-0 shadow text-start">
                                            <div class="modal-header border-bottom py-2.5 px-3">
                                                <h6 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Hapus Foto?</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3 text-center">
                                                <img src="{{ asset($g->foto) }}" class="rounded-3 w-100 mb-3" style="height:120px;object-fit:cover;">
                                                <p class="small text-muted mb-3">Apakah Anda yakin ingin menghapus foto ini secara permanen?</p>
                                                <form action="{{ route('admin.galeri.destroy', $g->id_galeri) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger w-100 rounded-pill btn-sm fw-bold">Ya, Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada foto galeri.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
