@extends('layouts.admin')

@section('title', 'Kelola Kode Diskon')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Kode Diskon</h3>
        <p class="text-muted mb-0">Kelola kupon potongan harga untuk reservasi glamping.</p>
    </div>
    <div class="col-auto">
        <button class="btn text-white rounded-pill px-4" style="background:#387478;" data-bs-toggle="modal" data-bs-target="#modalTambahDiskon">
            <i class="bi bi-plus-circle me-1"></i>Tambah Diskon
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
                        <th>Kode Diskon</th>
                        <th>Potongan Harga</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diskons as $index => $d)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-dark text-white fs-6 px-3 py-2 rounded-3 font-monospace">{{ $d->kode_diskon }}</span>
                            </td>
                            <td class="fw-bold" style="color:#387478;">
                                Rp {{ number_format($d->nilai_diskon, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($d->status == 'aktif')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Aktif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-outline-dark rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditDiskon{{ $d->id_diskon }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.diskon.destroy', $d->id_diskon) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kode diskon ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="modalEditDiskon{{ $d->id_diskon }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="{{ route('admin.diskon.update', $d->id_diskon) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom py-3">
                                            <h5 class="modal-title fw-bold" style="color:#243642;">Edit Kode Diskon</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Kode Diskon</label>
                                                <input type="text" name="kode_diskon" class="form-control rounded-3 text-uppercase" value="{{ $d->kode_diskon }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nilai Potongan Harga (Rp)</label>
                                                <input type="number" name="nilai_diskon" class="form-control rounded-3" value="{{ number_format($d->nilai_diskon, 0, '', '') }}" step="any" required placeholder="Contoh: 50000">
                                               
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Status</label>
                                                <select name="status" class="form-select rounded-3" required>
                                                    <option value="aktif" {{ $d->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="nonaktif" {{ $d->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                                </select>
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
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada kode diskon terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahDiskon" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('admin.diskon.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" style="color:#243642;">Tambah Kode Diskon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Diskon</label>
                        <input type="text" name="kode_diskon" class="form-control rounded-3 text-uppercase" placeholder="" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nilai Potongan Harga (Rp)</label>
                        <input type="number" name="nilai_diskon" class="form-control rounded-3" placeholder="" step="any" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select rounded-3" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
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
