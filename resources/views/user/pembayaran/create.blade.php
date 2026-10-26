@extends('layouts.user')

@section('title', 'Pembayaran DP Pemesanan')

@section('content')
<div class="bg-light py-4" style="min-height:85vh;">
    <div class="container py-2">

        <div class="row g-4">
            {{-- Form Pembayaran --}}
            <div class="col-lg-7">
                
                @if(session('info'))
                    <div class="alert alert-info border-0 rounded-3 shadow-sm mb-4" style="background:#EFF6FF;color:#1E40AF;border:1px solid #BFDBFE!important;">
                        <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if($pemesanan->pembayaran && $pemesanan->pembayaran->status_verifikasi == 'Ditolak')
                    <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4 p-3" style="background:#FEF2F2;color:#991B1B;border:1px solid #FCA5A5!important;">
                        <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>Pembayaran Sebelumnya Ditolak Admin</h6>
                        <p class="mb-0 small opacity-90">Bukti transfer yang Anda kirimkan sebelumnya tidak sesuai atau kurang jelas.</p>
                    </div>
                @endif

                {{-- Hitung Mundur --}}
                <div id="countdownAlertCard" class="card border rounded-3 p-3 mb-4 bg-white shadow-sm" style="border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Batas Waktu Pembayaran DP</h6>
                            <small class="text-muted" style="font-size:0.82rem;">Selesaikan pembayaran DP 50% sebelum batas waktu berakhir</small>
                        </div>
                        <div class="bg-light border rounded px-3 py-1.5 font-monospace fw-bold fs-5 text-dark" id="countdownTimer">
                            00:00:00
                        </div>
                    </div>
                </div>

                {{-- Waktu Habis --}}
                <div id="expiredBannerCard" class="card border-0 rounded-3 p-4 mb-4 bg-danger text-white shadow-sm d-none">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1">Batas Waktu Telah Habis</h5>
                            <p class="mb-0 small opacity-90">Pemesanan ini telah dibatalkan secara otomatis.</p>
                        </div>
                        <a href="{{ route('glamping.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-danger text-nowrap">
                            Pesan Ulang
                        </a>
                    </div>
                </div>

                {{-- Form Pembayaran --}}
                <div class="card border shadow-sm rounded-4 bg-white overflow-hidden mb-4" style="border-color:#e2e8f0!important;">
                    <div class="card-header bg-white p-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-dark">Pembayaran Uang Muka (DP 50%)</h5>
                    </div>

                    <div class="card-body p-4">
                        <form id="paymentForm" action="{{ route('pembayaran.store', $pemesanan->id_pemesanan) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="tipe_pembayaran" value="dp">

                            {{-- Nominal DP --}}
                            <label class="form-label fw-bold mb-2 text-dark">1. Nominal Pembayaran DP (50%)</label>
                            
                            <div class="mb-4">
                                <div class="card-option p-3 rounded-3 border bg-white" id="opt-dp" style="border-color:#387478!important;background:#F8FAFC;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-dark mb-1"><i class="bi bi-shield-check text-success me-1"></i>Uang Muka (DP 50%) Wajib</div>
                                            <small class="text-muted d-block" style="font-size:0.8rem;">Pelunasan sisa 50% dilakukan secara tunai/nontunai saat check-in di lokasi.</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 mb-1">DP 50%</span>
                                            <div class="fw-bold text-success fs-4">Rp {{ number_format($pemesanan->total_harga * 0.5, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Metode Pembayaran --}}
                            <label class="form-label fw-bold mb-3 text-dark">2. Metode Pembayaran</label>

                            {{-- Transfer Bank --}}
                            <div class="p-3 rounded-3 border mb-3 cursor-pointer method-card" id="methodCardManual" style="border-color:#387478!important;background:#FAFAFA;">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="method_manual" value="manual" checked>
                                    <label class="form-check-label w-100 cursor-pointer" for="method_manual">
                                        <div class="fw-bold text-dark mb-1">Transfer Bank (Upload Bukti Transfer)</div>
                                        <small class="text-muted d-block mb-3">Transfer ke rekening berikut lalu unggah struk pembayaran DP:</small>
                                    </label>
                                </div>

                                <div id="manualBankDetails" class="ps-4">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3 border bg-white">
                                                <small class="text-muted d-block mb-1">Bank BCA</small>
                                                <div class="font-monospace fw-bold text-dark fs-6 mb-1">123-456-7890</div>
                                                <small class="text-muted">a.n. Glamping Danau Kembar</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3 border bg-white">
                                                <small class="text-muted d-block mb-1">Bank Mandiri</small>
                                                <div class="font-monospace fw-bold text-dark fs-6 mb-1">987-000-1234</div>
                                                <small class="text-muted">a.n. Glamping Danau Kembar</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold text-dark mb-1">Unggah Struk / Bukti Transfer DP <span class="text-danger">*</span></label>
                                        <input type="file" name="bukti_pembayaran" id="inputBukti" class="form-control rounded-3" accept="image/*" required>
                                        <small class="text-muted" style="font-size:0.78rem;">Format: JPG, PNG, JPEG (Maks. 2MB)</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Pembayaran Online --}}
                            <div class="p-3 rounded-3 border mb-4 cursor-pointer method-card" id="methodCardOnline" style="border-color:#E2E8F0;">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="method_online" value="online">
                                    <label class="form-check-label w-100 cursor-pointer" for="method_online">
                                        <div class="fw-bold text-dark mb-1">Pembayaran Instan (QRIS / Virtual Account / E-Wallet)</div>
                                        <small class="text-muted d-block mb-2">Verifikasi otomatis tanpa perlu konfirmasi manual.</small>
                                    </label>
                                </div>

                                <div id="onlineChannelBadges" class="ps-4 d-none">
                                    <small class="text-muted d-block pt-1">
                                        Mendukung: QRIS (GoPay, DANA, OVO, ShopeePay), Virtual Account (BCA, Mandiri, BRI, BNI), & Kartu Kredit.
                                    </small>
                                </div>
                            </div>

                            {{-- Tombol Bayar --}}
                            <button type="submit" id="submitPayButton" class="btn text-white btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm" style="background:#387478;font-size:1rem;">
                                Bayar DP Sekarang (<span>Rp {{ number_format($pemesanan->total_harga * 0.5, 0, ',', '.') }}</span>)
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            {{-- Ringkasan Pemesanan --}}
            <div class="col-lg-5">
                <div class="card border shadow-sm rounded-4 bg-white sticky-top" style="top:90px; border-color:#E2E8F0!important; z-index:1;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="fw-bold mb-0 text-dark">Ringkasan Pemesanan</h6>
                    </div>
                    
                    <div class="card-body p-4">
                        @if($pemesanan->glamping)
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ asset($pemesanan->glamping->gambar ?? 'images/bg.jpeg') }}" class="rounded-3 me-3 flex-shrink-0" width="70" height="70" style="object-fit:cover;">
                                <div>
                                    <span class="badge bg-light text-dark border px-2 py-1 mb-1" style="font-size:10px;">
                                        {{ $pemesanan->glamping->tipeGlamping->nama_tipe ?? 'Glamping' }}
                                    </span>
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size:0.95rem;">{{ $pemesanan->glamping->nama_glamping }}</h6>
                                    <small class="text-muted" style="font-size:0.82rem;">{{ $pemesanan->jumlah_tamu }} Tamu</small>
                                </div>
                            </div>
                        @endif

                        <div class="p-3 rounded-3 bg-light border mb-4" style="font-size:0.85rem;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Check-in:</span>
                                <strong class="text-dark">{{ date('d M Y', strtotime($pemesanan->tanggal_checkin)) }} ({{ $pemesanan->jam_checkin ?? '14:00' }})</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Check-out:</span>
                                <strong class="text-dark">{{ date('d M Y', strtotime($pemesanan->tanggal_checkout)) }} (12:00)</strong>
                            </div>
                        </div>

                        {{-- Rincian Harga --}}
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-muted">Sewa Glamping:</span>
                            <span class="fw-semibold text-dark">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
                        </div>

                        @if($pemesanan->diskon || $pemesanan->promo)
                            @php
                                $dObj = $pemesanan->diskon ?? $pemesanan->promo;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2 small text-success">
                                <span>Diskon ({{ $dObj->kode_diskon ?? $dObj->kode_promo }}):</span>
                                <span class="fw-semibold">Terpasang</span>
                            </div>
                        @endif

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark">Total Tagihan:</span>
                            <span class="fw-bold fs-5 text-dark">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
                        </div>

                        <div class="p-3 rounded-3 mb-2 bg-light border border-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark small">Bayar DP 50% Sekarang:</span>
                                <span class="fw-bold text-success fs-5">Rp {{ number_format($pemesanan->total_harga * 0.5, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span>Sisa Pelunasan di Lokasi:</span>
                            <span>Rp {{ number_format($pemesanan->total_harga * 0.5, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    // Hitung mundur batas waktu pembayaran
    const createdTimestamp = {{ $pemesanan->created_at->timestamp }} * 1000;
    const expireTimestamp = createdTimestamp + (60 * 60 * 1000);

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expireTimestamp - now;

        if (distance < 0) {
            document.getElementById("countdownTimer").innerHTML = "EXPIRED";
            document.getElementById("countdownAlertCard").classList.add("d-none");
            document.getElementById("expiredBannerCard").classList.remove("d-none");
            
            const submitBtn = document.getElementById("submitPayButton");
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.replace("btn-success", "btn-secondary");
                submitBtn.innerText = 'Waktu Pembayaran Habis';
            }
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const pad = (num) => String(num).padStart(2, '0');
        document.getElementById("countdownTimer").innerHTML = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();

    let snapTokenDP = "{{ $snapTokenDP ?? '' }}";

    // Pilihan metode pembayaran (Manual vs Online)
    const radioManual = document.getElementById('method_manual');
    const radioOnline = document.getElementById('method_online');
    const cardManual = document.getElementById('methodCardManual');
    const cardOnline = document.getElementById('methodCardOnline');
    const bankDetails = document.getElementById('manualBankDetails');
    const onlineBadges = document.getElementById('onlineChannelBadges');
    const inputBukti = document.getElementById('inputBukti');

    function updateMethodStyle() {
        if (radioManual.checked) {
            cardManual.style.borderColor = '#387478';
            cardManual.style.background = '#FAFAFA';
            cardOnline.style.borderColor = '#E2E8F0';
            cardOnline.style.background = '#FFFFFF';
            bankDetails.classList.remove('d-none');
            onlineBadges.classList.add('d-none');
            if(inputBukti) inputBukti.required = true;
        } else {
            cardOnline.style.borderColor = '#387478';
            cardOnline.style.background = '#FAFAFA';
            cardManual.style.borderColor = '#E2E8F0';
            cardManual.style.background = '#FFFFFF';
            onlineBadges.classList.remove('d-none');
            bankDetails.classList.add('d-none');
            if(inputBukti) inputBukti.required = false;
        }
    }

    cardManual.addEventListener('click', function() {
        radioManual.checked = true;
        updateMethodStyle();
    });

    cardOnline.addEventListener('click', function() {
        radioOnline.checked = true;
        updateMethodStyle();
    });

    // Submisi form pembayaran
    const paymentForm = document.getElementById('paymentForm');
    const submitBtn = document.getElementById('submitPayButton');

    paymentForm.addEventListener('submit', function(e) {
        if (radioOnline.checked) {
            e.preventDefault();

            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';

            function triggerSnap(token) {
                snap.pay(token, {
                    onSuccess: function(result){
                        fetch("{{ route('pembayaran.konfirmasi', $pemesanan->id_pemesanan) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                tipe_pembayaran: 'dp',
                                metode_pembayaran: 'Midtrans Instant Payment'
                            })
                        })
                        .then(r => r.json())
                        .then(res => {
                            window.location.href = "{{ route('pembayaran.sukses', $pemesanan->id_pemesanan) }}";
                        })
                        .catch(err => {
                            window.location.href = "{{ route('pembayaran.sukses', $pemesanan->id_pemesanan) }}";
                        });
                    },
                    onPending: function(result){
                        window.location.href = "{{ route('pembayaran.instruksi', $pemesanan->id_pemesanan) }}";
                    },
                    onError: function(result){
                        alert('Pembayaran online tidak dapat dilanjutkan. Silakan gunakan Transfer Bank.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        radioManual.checked = true;
                        updateMethodStyle();
                    },
                    onClose: function(){
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
            }

            fetch("{{ route('pembayaran.snap_token', $pemesanan->id_pemesanan) }}?tipe_bayar=dp", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.snap_token) {
                    triggerSnap(data.snap_token);
                } else {
                    alert(data.error || 'Gagal mengambil token pembayaran. Silakan gunakan Transfer Bank.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    radioManual.checked = true;
                    updateMethodStyle();
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan koneksi. Silakan gunakan Transfer Bank.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                radioManual.checked = true;
                updateMethodStyle();
            });
        } else {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Mengunggah Bukti...';
        }
    });
</script>
@endsection
