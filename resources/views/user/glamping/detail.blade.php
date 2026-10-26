@extends('layouts.user')

@section('title', $glamping->nama_glamping . ' - Detail Unit')

@section('content')

@php
    $namaTipe = strtolower($glamping->tipeGlamping->nama_tipe ?? '');
    $namaUnit = strtolower($glamping->nama_glamping);
    $isGlamping = str_contains($namaTipe, 'glamping') || str_contains($namaUnit, 'glamping');
    $isFamilyRoom = str_contains($namaTipe, 'family') || str_contains($namaUnit, 'family');
    $isCabin = !$isGlamping; // Semua tipe selain Glamping (Cabin, Family Room, dll) tidak menggunakan Extra Bed
    $hargaExtrabedUnit = ($glamping->harga_extrabed && $glamping->harga_extrabed > 0) ? $glamping->harga_extrabed : 150000;
@endphp

{{-- Detail Unit --}}
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('glamping.index') }}" class="text-decoration-none text-muted">Daftar Unit</a></li>
            <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page">{{ $glamping->nama_glamping }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Galeri & Deskripsi --}}
        <div class="col-lg-8">
            <div class="card border shadow-sm rounded-3 overflow-hidden mb-4 bg-white">
                {{-- Carousel Foto --}}
                <div id="carouselFotoUnit" class="carousel slide position-relative" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        {{-- Foto Utama --}}
                        <div class="carousel-item active">
                            <img src="{{ asset($glamping->gambar ?? 'images/bg.jpeg') }}" 
                                 class="d-block w-100 cursor-pointer" 
                                 style="height:420px; object-fit:cover; object-position:center;" 
                                 alt="{{ $glamping->nama_glamping }}"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#modalFotoDetailUtuh" 
                                 data-index="0"
                                 title="Klik untuk lihat foto utuh (dapat digeser)">
                        </div>
                        {{-- Galeri Foto --}}
                        @if($glamping->galeri && $glamping->galeri->count() > 0)
                            @foreach($glamping->galeri as $idx => $gFoto)
                                <div class="carousel-item">
                                    <img src="{{ asset($gFoto->foto) }}" 
                                         class="d-block w-100 cursor-pointer" 
                                         style="height:420px; object-fit:cover; object-position:center;" 
                                         alt="Foto {{ $glamping->nama_glamping }}"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#modalFotoDetailUtuh" 
                                         data-index="{{ $idx + 1 }}"
                                         title="Klik untuk lihat foto utuh (dapat digeser)">
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @if($glamping->galeri && $glamping->galeri->count() > 0)
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselFotoUnit" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselFotoUnit" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                        {{-- Indikator Carousel --}}
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#carouselFotoUnit" data-bs-slide-to="0" class="active"></button>
                            @foreach($glamping->galeri as $idx => $gFoto)
                                <button type="button" data-bs-target="#carouselFotoUnit" data-bs-slide-to="{{ $idx + 1 }}"></button>
                            @endforeach
                        </div>
                    @endif
                    {{-- Badge Tipe --}}
                    <span class="position-absolute top-0 start-0 m-3 badge bg-dark px-3 py-2" style="z-index:5;">
                        {{ $glamping->tipeGlamping->nama_tipe ?? 'Kategori Penginapan' }}
                    </span>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4 border-bottom pb-4">
                        <div>
                            <h2 class="fw-bold mb-2 text-dark">{{ $glamping->nama_glamping }}</h2>
                            <p class="text-muted small mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Danau Kembar, Solok, Sumatera Barat</p>
                        </div>
                        <div class="text-md-end">
                            <small class="text-muted d-block mb-1">Tarif Per Malam / Unit</small>
                            <h3 class="fw-bold text-success mb-0">Rp {{ number_format($glamping->harga, 0, ',', '.') }}</h3>
                        </div>
                    </div>

                    {{-- Info Kapasitas --}}
                    <div class="row g-3 mb-4 text-center">
                        @if($isFamilyRoom)
                            <div class="col-md-4 col-6">
                                <div class="p-3 border rounded-3 bg-light">
                                    <span class="fw-bold d-block text-dark small">Kapasitas Tamu</span>
                                    <small class="text-muted">Maks {{ $glamping->kapasitas ?? 5 }} Orang (Bebas Kombinasi)</small>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="p-3 border rounded-3 bg-light">
                                    <span class="fw-bold d-block text-dark small">Ketentuan Anak</span>
                                    <small class="text-muted">Bebas (Tanpa Batasan Anak)</small>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="p-3 border rounded-3 bg-light">
                                    <span class="fw-bold d-block text-dark small">Total Stok Unit</span>
                                    <small class="text-success fw-bold">{{ $glamping->jumlah_unit ?? 1 }} Unit Tersedia</small>
                                </div>
                            </div>
                        @else
                            <div class="col-md-3 col-6">
                                <div class="p-3 border rounded-3 bg-light">
                                    <span class="fw-bold d-block text-dark small">Kapasitas Dewasa</span>
                                    <small class="text-muted">{{ $glamping->kapasitas_dewasa ?? 2 }} Orang / Unit</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-3 border rounded-3 bg-light">
                                    <span class="fw-bold d-block text-dark small">Kapasitas Anak</span>
                                    <small class="text-muted">{{ $glamping->kapasitas_anak ?? 1 }} Anak (≤ {{ $glamping->batas_umur_anak ?? 6 }}thn)</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-3 border rounded-3 bg-light">
                                    <span class="fw-bold d-block text-dark small">Total Stok Unit</span>
                                    <small class="text-success fw-bold">{{ $glamping->jumlah_unit ?? 1 }} Unit Tersedia</small>
                                </div>
                            </div>
                            @if($isGlamping)
                                <div class="col-md-3 col-6">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <span class="fw-bold d-block text-dark small">Extra Bed</span>
                                        <small class="text-primary fw-semibold">Rp {{ number_format($hargaExtrabedUnit, 0, ',', '.') }} /unit</small>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Deskripsi Unit --}}
                    <h5 class="fw-bold mb-3 text-dark">Deskripsi Unit</h5>
                    <p class="text-muted lh-lg mb-4">{{ $glamping->deskripsi }}</p>

                    {{-- Fasilitas Penginapan --}}
                    <h5 class="fw-bold mb-3 text-dark">Fasilitas Penginapan</h5>
                    <small class="text-muted d-block mb-3">Klik foto fasilitas di bawah untuk melihat gambar lebih besar.</small>
                    <div class="row g-3 mb-4">
                        @forelse($glamping->fasilitas as $f)
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 d-flex align-items-center bg-light">
                                    @if($f->gambar)
                                        <img src="{{ asset($f->gambar) }}" 
                                             class="rounded-2 me-3 flex-shrink-0 border cursor-pointer" 
                                             width="55" height="55" 
                                             style="object-fit:cover; cursor:pointer;" 
                                             data-bs-toggle="modal" 
                                             data-bs-target="#modalFotoFasilitas" 
                                             data-img="{{ asset($f->gambar) }}" 
                                             data-title="{{ $f->nama }}" 
                                             data-desc="{{ $f->deskripsi ?? '' }}"
                                             title="Klik untuk memperbesar">
                                    @else
                                        <div class="rounded-2 d-flex align-items-center justify-content-center me-3 flex-shrink-0 bg-success text-white" style="width:55px; height:55px;">
                                            <i class="bi bi-check2-circle fs-4"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark small">{{ $f->nama }}</h6>
                                        @if($f->deskripsi)
                                            <small class="text-muted d-block lh-sm">{{ $f->deskripsi }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted small mb-0">Belum ada fasilitas yang ditambahkan untuk unit ini.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Ulasan Pengunjung --}}
                    <hr class="my-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-star-fill text-warning me-2"></i>Ulasan & Rating Pengunjung</h5>
                        @if(isset($avgRating) && $avgRating)
                            <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-1.5 rounded-pill fw-bold">
                                ⭐ {{ $avgRating }} / 5.0 ({{ $reviews->count() }} Ulasan)
                            </span>
                        @endif
                    </div>

                    <div class="mb-4">
                        @forelse($reviews as $rev)
                            <div class="p-3 border rounded-3 bg-light mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center me-2.5" style="width:36px; height:36px; font-size:0.9rem;">
                                            {{ strtoupper(substr($rev->user->nama ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0 small">{{ $rev->user->nama ?? 'Pengunjung' }}</h6>
                                            <small class="text-muted" style="font-size:0.75rem;">{{ date('d M Y', strtotime($rev->created_at)) }}</small>
                                        </div>
                                    </div>
                                    <div class="text-warning small">
                                        @for($r = 1; $r <= 5; $r++)
                                            @if($r <= $rev->rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star text-muted opacity-50"></i>
                                            @endif
                                        @endfor
                                        <strong class="text-dark ms-1 small">({{ $rev->rating }}.0)</strong>
                                    </div>
                                </div>

                                <p class="text-secondary small mb-1 lh-sm">{{ $rev->komentar }}</p>

                                @if($rev->balasan_admin)
                                    <div class="p-2.5 rounded-3 border mt-2" style="background:#F0FDF4; border-color:#DCFCE7!important;">
                                        <small class="fw-bold text-success d-block mb-1"><i class="bi bi-reply-fill me-1"></i>Tanggapan Pengelola Glamping:</small>
                                        <small class="text-dark d-block lh-sm" style="font-size:0.8rem;">{{ $rev->balasan_admin }}</small>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-4 border rounded-3 bg-light text-center">
                                <p class="text-muted small mb-0"><i class="bi bi-chat-left-text me-1"></i>Belum ada ulasan untuk unit glamping ini. Jadilah yang pertama memberikan ulasan setelah kunjungan Anda!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Reservasi --}}
        <div class="col-lg-4">
            <div class="card border shadow rounded-3 sticky-top bg-white" style="top:90px;">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0 text-dark">Form Reservasi</h5>
                    <small class="text-muted">Isi tanggal dan rincian tamu</small>
                </div>
                <div class="card-body p-4">
                    {{-- Status Ketersediaan --}}
                    <div id="availabilityStatus" class="mb-3 d-none"></div>

                    @auth
                        @if(in_array(Auth::user()->role, ['admin', 'pemilik']))
                            <div class="text-center py-4">
                                <p class="text-muted small">Anda login sebagai pengelola. Reservasi khusus untuk pengunjung.</p>
                            </div>
                        @else
                            <form action="{{ route('reservasi.store') }}" method="POST" id="formReservasi">
                                @csrf
                                @php
                                    // Jika jam sekarang >= 23:00 WIB, tanggal terdekat otomatis mulai besok
                                    $minCheckinDate = ((int)date('H') >= 23) ? date('Y-m-d', strtotime('+1 day')) : date('Y-m-d');
                                    $minCheckoutDate = date('Y-m-d', strtotime($minCheckinDate . ' +1 day'));
                                @endphp
                                <input type="hidden" name="id_glamping" value="{{ $glamping->id_glamping }}">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-muted">Tanggal Check-in</label>
                                    <input type="date" name="tanggal_checkin" id="tglCheckin" class="form-control py-2" required min="{{ $minCheckinDate }}" value="{{ request('tanggal_checkin', $minCheckinDate) }}">
                                    @if((int)date('H') >= 23)
                                        <small class="text-danger d-block mt-1" style="font-size:0.75rem;">
                                            <i class="bi bi-info-circle me-1"></i>Batas jam pemesanan hari ini (23:00 WIB) sudah lewat. Check-in terdekat mulai besok.
                                        </small>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-muted">Jam Check-in Estimasi</label>
                                    <select name="jam_checkin" id="selectJamCheckin" class="form-select py-2">
                                        <option value="14:00 WIB" selected>14:00 WIB (Standar Check-in)</option>
                                        <option value="15:00 WIB">15:00 WIB</option>
                                        <option value="16:00 WIB">16:00 WIB</option>
                                        <option value="17:00 WIB">17:00 WIB</option>
                                        <option value="18:00 WIB">18:00 WIB</option>
                                        <option value="19:00 WIB">19:00 WIB</option>
                                        <option value="20:00 WIB">20:00 WIB</option>
                                        <option value="21:00 WIB">21:00 WIB</option>
                                        <option value="22:00 WIB">22:00 WIB</option>
                                        <option value="23:00 WIB">23:00 WIB (Check-in Malam)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-muted">Tanggal Check-out</label>
                                    <input type="date" name="tanggal_checkout" id="tglCheckout" class="form-control py-2" required min="{{ $minCheckoutDate }}">
                                </div>

                                {{-- Jumlah Unit --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-muted">Jumlah Unit Dipesan</label>
                                    <select name="jumlah_unit_pesan" id="jumlahUnitPesan" class="form-select py-2" onchange="updateGuestCapacityLimits(); cekKetersediaan(); hitungTotal();">
                                        @for($u = 1; $u <= ($totalUnit ?? 1); $u++)
                                            <option value="{{ $u }}">{{ $u }} Unit Kamar</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted d-block mt-1" style="font-size:0.75rem;" id="infoPerUnitText">
                                        1 Unit = {{ $glamping->kapasitas_dewasa ?? 2 }} Dewasa + {{ $glamping->kapasitas_anak ?? 1 }} Anak (≤ {{ $glamping->batas_umur_anak ?? 6 }} thn).
                                    </small>
                                </div>

                                {{-- Tamu Dewasa & Anak --}}
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small text-muted">Jumlah Dewasa</label>
                                        <input type="number" name="jumlah_dewasa" id="jumlahDewasaInput" class="form-control py-2" value="1" min="1" required oninput="validateGuestInput()" onblur="if(this.value===''||parseInt(this.value)<1) this.value=1; validateGuestInput();">
                                        <small class="text-muted d-block" style="font-size:0.7rem;" id="infoDewasaMax">Maks: {{ $glamping->kapasitas_dewasa ?? 2 }} orang</small>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small text-muted">Jumlah Anak</label>
                                        <input type="number" name="jumlah_anak" id="jumlahAnakInput" class="form-control py-2" value="0" min="0" oninput="validateGuestInput()" onblur="if(this.value==='') this.value=0; validateGuestInput();">
                                        <small class="text-muted d-block" style="font-size:0.7rem;" id="infoAnakMax">Maks: {{ $glamping->kapasitas_anak ?? 1 }} anak (≤ {{ $glamping->batas_umur_anak ?? 6 }}thn)</small>
                                    </div>
                                </div>

                                {{-- Extra Bed --}}
                                @if($isGlamping)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-muted">Extra Bed (Opsional)</label>
                                        <input type="number" name="jumlah_extrabed" id="jumlahExtrabed" class="form-control py-2" value="0" min="0" placeholder="0" oninput="hitungTotal()">
                                        <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Biaya Rp {{ number_format($hargaExtrabedUnit, 0, ',', '.') }} / unit per malam.</small>
                                    </div>
                                @else
                                    <input type="hidden" name="jumlah_extrabed" id="jumlahExtrabed" value="0">
                                @endif

                                <div class="mb-4">
                                    <label class="form-label fw-semibold small text-muted">Kode Diskon (Opsional)</label>
                                    <div class="input-group">
                                        <input type="text" name="kode_diskon" id="inputKodeDiskon" class="form-control text-uppercase py-2" placeholder="Contoh: PROMO50K" value="{{ old('kode_diskon') }}">
                                        <button class="btn btn-outline-secondary px-3 py-2 fw-semibold" type="button" id="btnCekDiskon" onclick="prosesCekDiskon()">Cek Kode</button>
                                    </div>
                                    <div id="diskonFeedback" class="mt-1 small fw-semibold" style="display:none;"></div>
                                </div>

                                {{-- Rincian Biaya --}}
                                <div class="p-3 rounded-3 mb-4 bg-light border">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted small">Rincian Sewa:</span>
                                        <span class="fw-bold text-dark small" id="rincianSewaText">1 Malam &times; 1 Unit</span>
                                    </div>
                                    @if($isGlamping)
                                        <div class="d-flex justify-content-between align-items-center mb-1 d-none" id="boxExtraBedRow">
                                            <span class="text-muted small">Biaya Extra Bed:</span>
                                            <span class="fw-semibold text-dark small" id="biayaExtrabedText">Rp 0</span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mb-1 d-none" id="boxDiskonRow">
                                        <span class="text-success small fw-semibold"><i class="bi bi-tag-fill me-1"></i>Potongan Diskon:</span>
                                        <span class="fw-bold text-success small" id="potonganDiskonText">-Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <span class="fw-bold text-dark">Total Biaya:</span>
                                        <span class="fw-bold fs-4 text-success" id="totalBayarText">Rp {{ number_format($glamping->harga, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <button type="submit" id="btnSubmit" class="btn text-white w-100 py-3 fw-bold" style="background:#387478;">Lanjutkan Pemesanan</button>
                            </form>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted small mb-3">Silakan login terlebih dahulu untuk melakukan reservasi unit.</p>
                            <a href="{{ route('login') }}" class="btn text-white px-4 py-2 fw-semibold w-100" style="background:#387478;">Login Sekarang</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Foto Fasilitas --}}
<div class="modal fade" id="modalFotoFasilitas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-3 shadow">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold text-dark" id="modalFotoTitle">Foto Fasilitas</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="modalFotoImg" src="" class="img-fluid rounded-3 mb-3 border shadow-sm" style="max-height:75vh; width:auto; max-width:100%; object-fit:contain;">
                <h6 class="fw-bold text-dark mb-1" id="modalFotoNama"></h6>
                <p class="text-muted small mb-0" id="modalFotoDesc"></p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Foto Detail --}}
<div class="modal fade" id="modalFotoDetailUtuh" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-4 shadow bg-dark text-white">
            <div class="modal-header border-bottom border-secondary py-3">
                <h6 class="modal-title fw-bold text-white" id="modalFotoDetailTitle">
                    <i class="bi bi-images me-2 text-success"></i>Galeri {{ $glamping->nama_glamping }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0 position-relative">
                <div id="carouselLightboxModal" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner py-4">
                        {{-- Foto Utama --}}
                        <div class="carousel-item active">
                            <img src="{{ asset($glamping->gambar ?? 'images/bg.jpeg') }}" class="img-fluid rounded-3 shadow" style="max-height:78vh; width:auto; max-width:100%; object-fit:contain;" alt="{{ $glamping->nama_glamping }}">
                            <div class="mt-2 text-center small text-light opacity-75">Foto Utama - {{ $glamping->nama_glamping }}</div>
                        </div>
                        {{-- Foto Galeri --}}
                        @if($glamping->galeri && $glamping->galeri->count() > 0)
                            @foreach($glamping->galeri as $idx => $gFoto)
                                <div class="carousel-item">
                                    <img src="{{ asset($gFoto->foto) }}" class="img-fluid rounded-3 shadow" style="max-height:78vh; width:auto; max-width:100%; object-fit:contain;" alt="Galeri {{ $glamping->nama_glamping }}">
                                    <div class="mt-2 text-center small text-light opacity-75">Foto Galeri {{ $idx + 1 }} dari {{ $glamping->galeri->count() }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @if($glamping->galeri && $glamping->galeri->count() > 0)
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselLightboxModal" data-bs-slide="prev" style="width:8%;">
                            <span class="carousel-control-prev-icon p-3 rounded-circle bg-dark bg-opacity-75 border" aria-hidden="true"></span>
                            <span class="visually-hidden">Sebelumnya</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselLightboxModal" data-bs-slide="next" style="width:8%;">
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
    // Data dari server
    const ketersediaanData = @json($calendarData);
    const totalUnitStok = {{ $totalUnit }};
    const hargaPerMalam = {{ $glamping->harga }};
    const hargaExtrabedPerMalam = {{ $hargaExtrabedUnit }};
    const kapasitasDewasaBase = {{ $glamping->kapasitas_dewasa ?? 2 }};
    const kapasitasAnakBase = {{ $glamping->kapasitas_anak ?? 1 }};
    const totalKapasitasUnit = {{ $glamping->kapasitas ?? 5 }};
    const isCabin = {{ $isCabin ? 'true' : 'false' }};
    const isGlamping = {{ $isGlamping ? 'true' : 'false' }};
    const isFamilyRoom = {{ $isFamilyRoom ? 'true' : 'false' }};

    // Elemen DOM
    const tglCheckin = document.getElementById('tglCheckin');
    const tglCheckout = document.getElementById('tglCheckout');
    const unitSelect = document.getElementById('jumlahUnitPesan');
    const dewasaInput = document.getElementById('jumlahDewasaInput');
    const anakInput = document.getElementById('jumlahAnakInput');
    const extrabedInput = document.getElementById('jumlahExtrabed');

    const totalText = document.getElementById('totalBayarText');
    const rincianText = document.getElementById('rincianSewaText');
    const biayaExtrabedText = document.getElementById('biayaExtrabedText');
    const statusDiv = document.getElementById('availabilityStatus');
    const btnSubmit = document.getElementById('btnSubmit');

    function changeMainImg(src) {
        const mainImg = document.getElementById('mainGlampingImg');
        if (mainImg) {
            mainImg.src = src;
        }
    }

    // Modal Foto Lightbox Listener & Date Config
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
                document.getElementById('modalFotoDesc').innerText = desc || 'Tidak ada deskripsi.';
            });
        }

        const modalFotoDetail = document.getElementById('modalFotoDetailUtuh');
        if (modalFotoDetail) {
            modalFotoDetail.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const slideIndex = button ? (button.getAttribute('data-index') || 0) : 0;
                const carouselEl = document.getElementById('carouselLightboxModal');
                if (carouselEl && window.bootstrap) {
                    const carouselInstance = bootstrap.Carousel.getOrCreateInstance(carouselEl);
                    carouselInstance.to(parseInt(slideIndex) || 0);
                }
            });
        }

        if (tglCheckin) {
            tglCheckin.addEventListener('change', function() {
                if (tglCheckout) {
                    const checkinDate = new Date(this.value);
                    checkinDate.setDate(checkinDate.getDate() + 1);
                    const nextDay = checkinDate.toISOString().split('T')[0];
                    tglCheckout.min = nextDay;
                    if (tglCheckout.value && tglCheckout.value <= this.value) {
                        tglCheckout.value = nextDay;
                    }
                }
                cekKetersediaan();
                hitungTotal();
            });
        }
        if (tglCheckout) {
            tglCheckout.addEventListener('change', function() {
                cekKetersediaan();
                hitungTotal();
            });
        }

        updateGuestCapacityLimits();
    });

    // Konstanta Glamping: 1 Extra Bed muat 2 orang, maks 2 Extra Bed (4 orang tambahan) per unit
    const maxExtraBedPerUnit = 2; // Maks 2 matras extra bed per unit
    const extraBedCapacity = 2;   // 1 Extra Bed untuk 2 orang
    const maxTambahanOrang = maxExtraBedPerUnit * extraBedCapacity; // 4 orang tambahan

    // Update batas kapasitas tamu berdasarkan jumlah unit
    function updateGuestCapacityLimits() {
        if (!unitSelect) return;
        const units = parseInt(unitSelect.value) || 1;

        if (isFamilyRoom) {
            const maxTotal = units * totalKapasitasUnit;
            if (dewasaInput) dewasaInput.max = 20;
            if (anakInput) anakInput.max = 20;

            const infoDewasa = document.getElementById('infoDewasaMax');
            const infoAnak = document.getElementById('infoAnakMax');
            if (infoDewasa) infoDewasa.innerText = 'Kombinasi total maks ' + maxTotal + ' org';
            if (infoAnak) infoAnak.innerText = 'Bebas tanpa batasan anak';

            const infoPerUnit = document.getElementById('infoPerUnitText');
            if (infoPerUnit) {
                infoPerUnit.innerText = units + ' Unit = Maksimal ' + maxTotal + ' Orang (Bebas kombinasi Dewasa & Anak)';
            }
        } else {
            const baseDewasa = units * kapasitasDewasaBase;
            const maxAnakDisplay = units * kapasitasAnakBase;
            const maxDewasa = isGlamping ? (baseDewasa + (maxTambahanOrang * units)) : baseDewasa;

            if (dewasaInput) dewasaInput.max = 20;
            if (anakInput) {
                anakInput.max = 20;
            }

            const infoDewasa = document.getElementById('infoDewasaMax');
            const infoAnak = document.getElementById('infoAnakMax');
            if (infoDewasa) {
                if (isGlamping) {
                    infoDewasa.innerText = 'Standar ' + baseDewasa + ' org, maks ' + maxDewasa + ' org (dgn Extra Bed)';
                } else {
                    infoDewasa.innerText = 'Maks ' + baseDewasa + ' orang untuk ' + units + ' unit';
                }
            }
            if (infoAnak) {
                infoAnak.innerText = 'Maks ' + maxAnakDisplay + ' anak (≤ {{ $glamping->batas_umur_anak ?? 6 }}thn)';
            }
            const infoPerUnit = document.getElementById('infoPerUnitText');
            if (infoPerUnit) {
                if (isGlamping) {
                    infoPerUnit.innerText = units + ' Unit = ' + baseDewasa + ' Dewasa Standar (+ Maks ' + (units * maxExtraBedPerUnit) + ' Extra Bed untuk ' + (units * maxTambahanOrang) + ' org) + ' + maxAnakDisplay + ' Anak';
                } else {
                    infoPerUnit.innerText = units + ' Unit = ' + baseDewasa + ' Dewasa + ' + maxAnakDisplay + ' Anak (≤ {{ $glamping->batas_umur_anak ?? 6 }} thn)';
                }
            }
        }
    }

    function validateGuestInput() {
        if (!unitSelect) return;
        if (dewasaInput && (dewasaInput.value === '' || isNaN(parseInt(dewasaInput.value)))) return;
        if (anakInput && (anakInput.value === '' || isNaN(parseInt(anakInput.value)))) return;

        const valDewasa = parseInt(dewasaInput.value) || 1;
        const valAnak = parseInt(anakInput.value) || 0;
        const totalTamu = valDewasa + valAnak;

        let unitDibutuhkan = 1;
        if (isFamilyRoom) {
            unitDibutuhkan = Math.ceil(totalTamu / totalKapasitasUnit);
        } else if (isCabin) {
            const safeKapAnak = Math.max(1, kapasitasAnakBase);
            const unitDibutuhkanDewasa = Math.ceil(valDewasa / kapasitasDewasaBase);
            const unitDibutuhkanAnak = valAnak > 0 ? Math.ceil(valAnak / safeKapAnak) : 1;
            unitDibutuhkan = Math.max(1, Math.max(unitDibutuhkanDewasa, unitDibutuhkanAnak));
        } else if (isGlamping) {
            const safeKapAnak = Math.max(1, kapasitasAnakBase);
            const maxPerUnitDewasa = kapasitasDewasaBase + maxTambahanOrang; // 2 + 4 = 6
            const unitDibutuhkanDewasa = Math.ceil(valDewasa / maxPerUnitDewasa);
            const unitDibutuhkanAnak = valAnak > 0 ? Math.ceil(valAnak / safeKapAnak) : 1;
            unitDibutuhkan = Math.max(1, Math.max(unitDibutuhkanDewasa, unitDibutuhkanAnak));
        }

        unitDibutuhkan = Math.max(1, unitDibutuhkan);

        const currentUnits = parseInt(unitSelect.value) || 1;
        // Hanya naikkan unit jika tamu membutuhkan lebih banyak unit dari yang dipilih
        // Jangan turunkan unit yang sudah dipilih manual oleh user
        if (unitDibutuhkan > currentUnits) {
            if (unitDibutuhkan <= totalUnitStok) {
                unitSelect.value = unitDibutuhkan;
            } else {
                unitSelect.value = totalUnitStok;
            }
        }

        updateGuestCapacityLimits();

        if (isGlamping && extrabedInput) {
            const unitsNow = parseInt(unitSelect.value) || 1;
            const newBaseDewasa = unitsNow * kapasitasDewasaBase;
            if (valDewasa > newBaseDewasa) {
                const extraPeople = valDewasa - newBaseDewasa;
                extrabedInput.value = Math.ceil(extraPeople / extraBedCapacity);
            } else {
                extrabedInput.value = 0;
            }
        }

        hitungTotal();
        cekKetersediaan();
    }

    let nilaiDiskonAktif = 0;

    // Hitung total harga
    function hitungTotal() {
        if (!unitSelect) return;
        const units = parseInt(unitSelect.value) || 1;
        const extrabed = parseInt(extrabedInput ? extrabedInput.value : 0) || 0;

        let malam = 1;
        if (tglCheckin && tglCheckout && tglCheckin.value && tglCheckout.value) {
            const start = new Date(tglCheckin.value);
            const end = new Date(tglCheckout.value);
            malam = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            if (malam <= 0) malam = 1;
        }

        const biayaKamar = malam * hargaPerMalam * units;
        const biayaEB = malam * hargaExtrabedPerMalam * extrabed;
        const subtotal = biayaKamar + biayaEB;
        const total = Math.max(0, subtotal - nilaiDiskonAktif);

        if (totalText) totalText.innerText = 'Rp ' + total.toLocaleString('id-ID');
        if (rincianText) rincianText.innerText = malam + ' Malam \u00D7 ' + units + ' Unit';
        if (biayaExtrabedText) biayaExtrabedText.innerText = 'Rp ' + biayaEB.toLocaleString('id-ID');

        const boxRow = document.getElementById('boxExtraBedRow');
        if (boxRow) {
            boxRow.classList.toggle('d-none', extrabed <= 0);
        }

        const boxDiskon = document.getElementById('boxDiskonRow');
        const potonganText = document.getElementById('potonganDiskonText');
        if (boxDiskon && potonganText) {
            if (nilaiDiskonAktif > 0) {
                boxDiskon.classList.remove('d-none');
                potonganText.innerText = '-Rp ' + nilaiDiskonAktif.toLocaleString('id-ID');
            } else {
                boxDiskon.classList.add('d-none');
            }
        }
    }

    // Cek Kode Diskon via AJAX (Real-time tanpa Reload Halaman)
    function prosesCekDiskon() {
        const kodeInput = document.getElementById('inputKodeDiskon');
        const feedbackDiv = document.getElementById('diskonFeedback');
        const btnCek = document.getElementById('btnCekDiskon');
        if (!kodeInput || !feedbackDiv) return;

        const kode = kodeInput.value.trim();
        if (!kode) {
            feedbackDiv.style.display = 'block';
            feedbackDiv.className = 'mt-1 small fw-semibold text-danger';
            feedbackDiv.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Silakan ketik kode diskon terlebih dahulu.';
            nilaiDiskonAktif = 0;
            hitungTotal();
            return;
        }

        btnCek.disabled = true;
        btnCek.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Cek...';

        fetch('{{ route("diskon.cek") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ kode_diskon: kode })
        })
        .then(res => res.json())
        .then(data => {
            btnCek.disabled = false;
            btnCek.innerHTML = 'Cek Kode';
            feedbackDiv.style.display = 'block';

            if (data.valid) {
                nilaiDiskonAktif = data.nilai_diskon || 0;
                feedbackDiv.className = 'mt-1 small fw-semibold text-success';
                feedbackDiv.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + data.message;
            } else {
                nilaiDiskonAktif = 0;
                feedbackDiv.className = 'mt-1 small fw-semibold text-danger';
                feedbackDiv.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> ' + data.message;
            }
            hitungTotal();
        })
        .catch(err => {
            btnCek.disabled = false;
            btnCek.innerHTML = 'Cek Kode';
            feedbackDiv.style.display = 'block';
            feedbackDiv.className = 'mt-1 small fw-semibold text-danger';
            feedbackDiv.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Gagal memeriksa kode diskon.';
            nilaiDiskonAktif = 0;
            hitungTotal();
        });
    }

    // Cek ketersediaan unit pada tanggal yang dipilih
    function cekKetersediaan() {
        if (!statusDiv || !tglCheckin || !tglCheckout) return;
        if (!tglCheckin.value || !tglCheckout.value) {
            statusDiv.classList.add('d-none');
            return;
        }

        // Cari jumlah unit terpakai terbanyak dalam rentang tanggal
        let maxTerpakai = 0;
        let d = new Date(tglCheckin.value);
        const endD = new Date(tglCheckout.value);

        while (d < endD) {
            const tgl = d.toISOString().split('T')[0];
            if (ketersediaanData[tgl] !== undefined && ketersediaanData[tgl] > maxTerpakai) {
                maxTerpakai = ketersediaanData[tgl];
            }
            d.setDate(d.getDate() + 1);
        }

        const sisa = totalUnitStok - maxTerpakai;
        const diminta = parseInt(unitSelect.value) || 1;
        const valDewasa = parseInt(dewasaInput ? dewasaInput.value : 0) || 0;
        const valAnak = parseInt(anakInput ? anakInput.value : 0) || 0;
        const totalTamu = valDewasa + valAnak;

        const maxKapasitasDewasaAllowed = isFamilyRoom ? (diminta * totalKapasitasUnit) : (diminta * (kapasitasDewasaBase + (isGlamping ? maxTambahanOrang : 0)));
        const maxKapasitasAnakAllowed = isFamilyRoom ? (diminta * totalKapasitasUnit) : (diminta * kapasitasAnakBase);

        statusDiv.classList.remove('d-none', 'alert-success', 'alert-warning', 'alert-danger');

        if (sisa <= 0) {
            statusDiv.classList.add('alert', 'alert-danger', 'p-2', 'small');
            statusDiv.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> <strong>Penuh!</strong> Semua unit sudah terpesan untuk tanggal tersebut.';
            if (btnSubmit) btnSubmit.disabled = true;
        } else if (!isFamilyRoom && (valDewasa > maxKapasitasDewasaAllowed || valAnak > maxKapasitasAnakAllowed)) {
            statusDiv.classList.add('alert', 'alert-danger', 'p-2', 'small');
            statusDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Jumlah tamu (' + valDewasa + ' Dewasa, ' + valAnak + ' Anak) melebihi kapasitas untuk ' + diminta + ' unit (Maks ' + maxKapasitasDewasaAllowed + ' Dewasa & ' + maxKapasitasAnakAllowed + ' Anak). Silakan tambah jumlah unit kamar.';
            if (btnSubmit) btnSubmit.disabled = true;
        } else if (isFamilyRoom && (totalTamu > maxKapasitasDewasaAllowed)) {
            statusDiv.classList.add('alert', 'alert-danger', 'p-2', 'small');
            statusDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Total tamu (' + totalTamu + ' orang) melebihi kapasitas maksimal (' + maxKapasitasDewasaAllowed + ' orang) untuk ' + diminta + ' unit. Silakan tambah jumlah unit kamar.';
            if (btnSubmit) btnSubmit.disabled = true;
        } else if (diminta > sisa) {
            statusDiv.classList.add('alert', 'alert-warning', 'p-2', 'small');
            statusDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Hanya tersisa <strong>' + sisa + ' unit</strong> untuk tanggal tersebut.';
            if (btnSubmit) btnSubmit.disabled = false;
        } else {
            statusDiv.classList.add('alert', 'alert-success', 'p-2', 'small');
            statusDiv.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Tersedia <strong>' + sisa + ' unit</strong> untuk tanggal yang dipilih.';
            if (btnSubmit) btnSubmit.disabled = false;
        }
    }

    // Menonaktifkan opsi jam check-in yang sudah lewat untuk hari ini
    const selectJamCheckin = document.getElementById('selectJamCheckin');

    function updateJamCheckinOptions() {
        if (!tglCheckin || !selectJamCheckin) return;
        const selectedDate = tglCheckin.value;
        const todayObj = new Date();
        const year = todayObj.getFullYear();
        const month = String(todayObj.getMonth() + 1).padStart(2, '0');
        const day = String(todayObj.getDate()).padStart(2, '0');
        const todayStr = `${year}-${month}-${day}`;
        const currentHour = todayObj.getHours();

        let firstAvailableIndex = -1;

        Array.from(selectJamCheckin.options).forEach((opt, index) => {
            const hourVal = parseInt(opt.value);
            if (selectedDate === todayStr && hourVal <= currentHour) {
                opt.disabled = true;
                opt.text = opt.value + ' (Sudah Lewat)';
            } else {
                opt.disabled = false;
                if (opt.value === '14:00 WIB') opt.text = '14:00 WIB (Standar Check-in)';
                else if (opt.value === '23:00 WIB') opt.text = '23:00 WIB (Check-in Malam)';
                else opt.text = opt.value;

                if (firstAvailableIndex === -1) firstAvailableIndex = index;
            }
        });

        if (selectJamCheckin.selectedIndex !== -1 && selectJamCheckin.options[selectJamCheckin.selectedIndex].disabled) {
            if (firstAvailableIndex !== -1) {
                selectJamCheckin.selectedIndex = firstAvailableIndex;
            }
        }
    }

    // Matikan fitur scroll mouse wheel pada input type="number" agar angka tidak berubah saat di-scroll
    document.addEventListener('wheel', function(e) {
        if (document.activeElement && document.activeElement.type === 'number') {
            document.activeElement.blur();
        }
    });

    if (tglCheckin) {
        tglCheckin.addEventListener('change', updateJamCheckinOptions);
        updateJamCheckinOptions();
    }
</script>
@endsection
