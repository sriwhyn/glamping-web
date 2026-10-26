@extends('layouts.user')

@section('title', 'Kontak & Ulasan')

@section('content')
<div class="py-4 bg-light border-bottom">
    <div class="container text-center">
        <h2 class="fw-bold mb-2 text-dark">Kontak & Lokasi Kami</h2>
        <p class="text-muted mb-0">Hubungi pengelola atau berikan ulasan mengenai layanan Glamping Danau Kembar.</p>
    </div>
</div>

<div class="container py-5">
    {{-- Akses Cepat --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="https://wa.me/6281372220567" target="_blank" class="card border text-decoration-none p-3 h-100 bg-white shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 text-white me-3 bg-success flex-shrink-0">
                        <i class="bi bi-whatsapp fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">WhatsApp Respon Cepat</small>
                        <strong class="text-dark d-block">0813-7222-0567</strong>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="tel:081292389075" class="card border text-decoration-none p-3 h-100 bg-white shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 text-white me-3 bg-primary flex-shrink-0">
                        <i class="bi bi-telephone-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Telepon Pengelola</small>
                        <strong class="text-dark d-block">0813-7222-0567</strong>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="https://maps.google.com/?q=Danau+Kembar+Solok" target="_blank" class="card border text-decoration-none p-3 h-100 bg-white shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 text-white me-3 bg-danger flex-shrink-0">
                        <i class="bi bi-geo-alt-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Buka Petunjuk Rute</small>
                        <strong class="text-dark d-block">Google Maps Danau Kembar</strong>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        {{-- Informasi Kontak --}}
        <div class="col-lg-5">
            <div class="card border rounded-3 p-4 bg-white h-100 shadow-sm">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-info-circle me-2"></i>Informasi Alamat & Layanan</h5>

                <div class="mb-3">
                    <label class="fw-semibold text-muted small d-block">Alamat Lokasi</label>
                    <p class="text-dark mb-0">Kawasan Wisata Danau Kembar, Nagari Alahan Panjang, Kabupaten Solok, Sumatera Barat</p>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold text-muted small d-block">Telepon / WhatsApp</label>
                    <p class="text-dark mb-0">0812-9238-9075</p>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold text-muted small d-block">Email Resmi</label>
                    <p class="text-dark mb-0">info@glampingdanaukembar.com</p>
                </div>

                <div>
                    <label class="fw-semibold text-muted small d-block">Jam Layanan</label>
                    <p class="text-dark mb-0">Setiap Hari: 08.00 - 22.00 WIB</p>
                </div>
            </div>
        </div>

        {{-- Form Ulasan --}}
        <div class="col-lg-7">
            <div class="card border rounded-3 p-4 bg-white shadow-sm">
                <h5 class="fw-bold mb-3 text-dark">Tulis Ulasan & Pertanyaan</h5>
                <form action="{{ route('review.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Rating (Bintang)</label>
                            <select name="rating" class="form-select py-2 rounded-3" required>
                                <option value="5">5 Bintang (Sangat Bagus)</option>
                                <option value="4">4 Bintang (Bagus)</option>
                                <option value="3">3 Bintang (Cukup)</option>
                                <option value="2">2 Bintang (Kurang)</option>
                                <option value="1">1 Bintang (Sangat Kurang)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Subjek Ulasan</label>
                            <input type="text" name="subjek" class="form-control py-2 rounded-3" placeholder="Contoh: Layanan / Kebersihan" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Isi Ulasan / Pertanyaan</label>
                            <textarea name="komentar" class="form-control py-2 rounded-3" rows="4" placeholder="Tuliskan ulasan Anda..." required></textarea>
                        </div>
                        <div class="col-12">
                            @auth
                                <button type="submit" class="btn text-white px-4 py-2 fw-semibold" style="background:#387478;">Kirim Ulasan</button>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-dark px-4 py-2 fw-semibold">Login untuk Kirim Ulasan</a>
                            @endauth
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Peta Lokasi --}}
    <div class="card border rounded-3 overflow-hidden bg-white mb-5 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-map me-2 text-danger"></i>Peta Lokasi Google Maps</h5>
            <a href="https://maps.google.com/?q=Danau+Kembar+Solok" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Aplikasi Google Maps
            </a>
        </div>
        <div class="card-body p-0">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15958.07727192451!2d100.67812975!3d-1.07742135!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2abec20593b4a9%3A0x6fb640243be1eb8!2sDanau%20Diatas!5e0!3m2!1sid!2sid!4v1700000000000!52m2!1sid!2sid" 
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>

    {{-- Daftar Ulasan --}}
    <div class="card border rounded-3 p-4 bg-white mb-4 shadow-sm">
        <h5 class="fw-bold mb-4 text-dark">Ulasan dari Pengunjung</h5>

        <div class="d-flex flex-column gap-3">
            @forelse($reviews as $r)
                <div class="p-3 border rounded-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark">{{ $r->user->nama ?? 'Pengunjung' }}</strong>
                        <small class="text-muted">{{ $r->created_at->format('d M Y') }}</small>
                    </div>
                    <div class="text-warning small mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star-fill {{ $i <= $r->rating ? '' : 'text-muted opacity-25' }}"></i>
                        @endfor
                        <span class="text-muted ms-1">({{ $r->rating }}/5)</span>
                    </div>
                    <p class="mb-1 text-secondary small">{{ $r->komentar }}</p>

                    {{-- Balasan Admin --}}
                    @if($r->balasan_admin)
                        <div class="mt-2 p-2 bg-white border-start border-3 border-success rounded-2 small">
                            <strong class="text-success d-block mb-1">Balasan Admin:</strong>
                            <p class="mb-0 text-muted">{{ $r->balasan_admin }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted text-center py-3 mb-0">Belum ada ulasan dari pengunjung.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
