@extends('layouts.user')

@section('title', 'Tentang Kami')

@section('content')
{{-- Header --}}
<div class="py-4 bg-light border-bottom">
    <div class="container text-center">
        <h2 class="fw-bold mb-2 text-dark">Tentang Kami</h2>
        <p class="text-muted mb-0">Glamping Danau Kembar Scenery - Alahan Panjang, Kabupaten Solok</p>
    </div>
</div>

<div class="container py-5">
    {{-- Profil --}}
    <div class="row align-items-center mb-5 g-4">
        <div class="col-lg-6">
            <img src="{{ asset('images//1785421053_bg.jpeg') }}" class="img-fluid rounded-3 border w-100" style="height:320px; object-fit:cover;" alt="Glamping Danau Kembar">
        </div>
        <div class="col-lg-6">
            <h4 class="fw-bold mb-3 text-dark">Pengalaman Menginap Alam Terbuka di Danau Kembar</h4>
            <p class="text-secondary lh-base mb-3">Glamping Danau Kembar Scenery hadir untuk memberikan fasilitas penginapan bernuansa alam terbuka yang nyaman dan aman bagi para pengunjung kawasan wisata Danau Kembar, Alahan Panjang.</p>
            <p class="text-secondary lh-base mb-0">Lokasi kami berada tepat di sekitar area danau dengan udara pegunungan yang sejuk, menyajikan tempat beristirahat yang cocok bagi keluarga, pasangan, maupun rombongan wisata.</p>
        </div>
    </div>

    {{-- Info Pengelola --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border rounded-3 h-100 p-4 bg-white">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person-circle me-2"></i>Pemilik</h5>
                <p class="text-secondary mb-0 lh-base fs-5">Nurhayati</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border rounded-3 h-100 p-4 bg-white">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-calendar-event me-2"></i>Berdiri Sejak</h5>
                <p class="text-secondary mb-0 lh-base fs-5">Agustus 2022</p>
            </div>
        </div>
    </div>
</div>
@endsection
