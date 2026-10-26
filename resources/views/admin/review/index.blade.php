@extends('layouts.admin')

@section('title', 'Kelola Review')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Review Pengunjung</h3>
        <p class="text-muted mb-0">Daftar ulasan, rating, dan tanggapan diskusi dari pengunjung beserta balasan pengelola.</p>
    </div>
</div>

<div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Pengunjung</th>
                        <th>Rating & Subjek</th>
                        <th>Isi Pesan / Ulasan</th>
                        <th>Balasan Admin</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $index => $r)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $r->user->nama ?? 'Pengunjung' }}</div>
                                <small class="text-muted">{{ $r->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="text-warning mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill {{ $i <= $r->rating ? '' : 'text-muted opacity-25' }}" style="font-size:12px;"></i>
                                    @endfor
                                    <span class="fw-bold text-dark small ms-1">({{ $r->rating }}/5)</span>
                                </div>
                                <span class="badge bg-light text-dark border">{{ $r->subjek ?? 'Ulasan' }}</span>
                            </td>
                            <td style="max-width:280px;">
                                <p class="mb-0 small text-dark lh-sm">{{ $r->komentar }}</p>
                                <small class="text-muted" style="font-size:11px;">{{ $r->created_at->format('d M Y, H:i') }} WIB</small>
                            </td>
                            <td style="max-width:250px;">
                                @if($r->balasan_admin)
                                    <div class="p-2 rounded-3 bg-light border border-success-subtle small">
                                        <strong class="text-success d-block mb-1"><i class="bi bi-reply-fill me-1"></i>Balasan Admin:</strong>
                                        <span class="text-dark">{{ $r->balasan_admin }}</span>
                                    </div>
                                    @if($r->balasan_user)
                                        <div class="p-2 rounded-3 bg-white border mt-1 small">
                                            <strong class="text-primary d-block mb-1"><i class="bi bi-person-fill me-1"></i>Respon User Kembali:</strong>
                                            <span class="text-dark">{{ $r->balasan_user }}</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Belum Dibalas</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" class="btn btn-sm text-white rounded-pill px-3" style="background:#387478;" data-bs-toggle="modal" data-bs-target="#modalBalas{{ $r->id_review }}">
                                        <i class="bi bi-reply-fill me-1"></i>Balas
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $r->id_review }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                {{-- Modal Balas Admin --}}
                                <div class="modal fade" id="modalBalas{{ $r->id_review }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-bottom py-3 px-4">
                                                <h5 class="modal-title fw-bold" style="color:#243642;"><i class="bi bi-reply-fill me-2 text-success"></i>Balas Review / Pertanyaan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.review.balas', $r->id_review) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4 text-start">
                                                    <div class="p-3 bg-light rounded-3 mb-3 border">
                                                        <strong class="d-block text-dark small mb-1">{{ $r->user->nama ?? 'Pengunjung' }} ({{ $r->subjek ?? 'Ulasan' }})</strong>
                                                        <p class="mb-0 small text-muted">"{{ $r->komentar }}"</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Tuliskan Balasan Admin <span class="text-danger">*</span></label>
                                                        <textarea name="balasan_admin" class="form-control rounded-3" rows="4" placeholder="Tuliskan jawaban atau ucapan terima kasih di sini..." required>{{ $r->balasan_admin }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top py-2 px-4">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background:#387478;">Kirim Balasan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Hapus Ulasan --}}
                                <div class="modal fade" id="modalDelete{{ $r->id_review }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-bottom py-2.5 px-3">
                                                <h6 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Hapus Review?</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3 text-start">
                                                <p class="small text-muted mb-3">Apakah Anda yakin ingin menghapus ulasan dari <strong>{{ $r->user->nama ?? '-' }}</strong>?</p>
                                                <form action="{{ route('admin.review.destroy', $r->id_review) }}" method="POST">
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
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada review atau pertanyaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
