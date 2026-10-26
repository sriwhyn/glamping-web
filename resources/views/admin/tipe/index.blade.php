@extends('layouts.admin')

@section('title', 'Tipe Glamping')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Kelola Tipe Glamping</h3>
        <p class="text-muted mb-0">Daftar kategori/tipe glamping yang tersedia.</p>
    </div>
    <div class="col-auto">
        <button type="button" class="btn text-white rounded-pill px-4" style="background:#387478;" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Tipe
        </button>
    </div>
</div>

<div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width:80px;">No</th>
                        <th>Nama Tipe</th>
                        <th class="text-center pe-4" style="width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tipes as $index => $t)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                            <td class="fw-bold" style="color:#243642;">{{ $t->nama_tipe }}</td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $t->id_tipe }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.tipe.destroy', $t->id_tipe) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin menghapus tipe ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="modalEdit{{ $t->id_tipe }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="{{ route('admin.tipe.update', $t->id_tipe) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="fw-bold" style="color:#243642;">Edit Tipe Glamping</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body py-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Tipe</label>
                                                <input type="text" name="nama_tipe" class="form-control rounded-3 py-2" value="{{ $t->nama_tipe }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn text-white rounded-pill px-4" style="background:#387478;">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada tipe glamping.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('admin.tipe.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold" style="color:#243642;">Tambah Tipe Glamping</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tipe</label>
                        <input type="text" name="nama_tipe" class="form-control rounded-3 py-2" placeholder="Contoh: Family Tent, Deluxe Cabin" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background:#387478;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
