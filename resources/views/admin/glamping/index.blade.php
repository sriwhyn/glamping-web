@extends('layouts.admin')

@section('title', 'Unit Glamping')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Kelola Unit Glamping</h3>
        <p class="text-muted mb-0">Daftar semua unit glamping yang terdaftar dalam sistem.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.glamping.create') }}" class="btn text-white rounded-pill px-4" style="background:#387478;">
            <i class="bi bi-plus-lg me-1"></i>Tambah Unit
        </a>
    </div>
</div>

<div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Gambar</th>
                        <th>Nama Unit</th>
                        <th>Tipe</th>
                        <th>Kapasitas</th>
                        <th>Jumlah Unit</th>
                        <th>Harga / Malam</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($glampings as $g)
                        <tr>
                            <td class="ps-4">
                                <img src="{{ asset($g->gambar ?? 'images/bg.jpeg') }}" 
                                     width="75" height="50" 
                                     class="rounded-3 border shadow-xs" 
                                     style="object-fit:cover; object-position:center; cursor:pointer;" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#modalPreviewFotoAdmin" 
                                     data-img="{{ asset($g->gambar ?? 'images/bg.jpeg') }}" 
                                     data-title="{{ $g->nama_glamping }}"
                                     title="Klik untuk melihat foto utuh">
                            </td>
                            <td class="fw-bold" style="color:#243642;">{{ $g->nama_glamping }}</td>
                            <td><span class="badge" style="background:#629584;color:#fff;">{{ $g->tipeGlamping->nama_tipe ?? '-' }}</span></td>
                            <td><i class="bi bi-people me-1"></i>{{ $g->kapasitas }} Orang</td>
                            <td><span class="badge bg-light text-dark border"><i class="bi bi-door-open me-1"></i>{{ $g->jumlah_unit ?? 1 }} Unit</span></td>
                            <td class="fw-bold" style="color:#387478;">Rp {{ number_format($g->harga, 0, ',', '.') }}</td>
                            <td>
                                @if($g->status == 'tersedia')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Tersedia</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Tidak Tersedia</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('admin.glamping.edit', $g->id_glamping) }}" class="btn btn-sm btn-outline-warning rounded-circle me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.glamping.destroy', $g->id_glamping) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus unit ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada unit glamping.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Foto --}}
<div class="modal fade" id="modalPreviewFotoAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark" id="modalFotoTitleAdmin">Foto Unit Glamping</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4 bg-dark rounded-bottom-4">
                <img id="modalFotoImgAdmin" src="" class="img-fluid rounded-3 shadow" style="max-height:75vh; max-width:100%; object-fit:contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalPreviewFotoAdmin');
        if (modal) {
            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imgSrc = button.getAttribute('data-img');
                const title = button.getAttribute('data-title');
                document.getElementById('modalFotoImgAdmin').src = imgSrc;
                document.getElementById('modalFotoTitleAdmin').innerText = title + ' - Foto Utuh';
            });
        }
    });
</script>
@endsection

