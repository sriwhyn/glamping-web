@extends('layouts.user')

@section('title', 'Instruksi Pembayaran')

@section('content')
<div class="py-4" style="background: #243642;">
    <div class="container text-center text-white">
        <h2 class="fw-bold mb-1">Instruksi Pembayaran</h2>
        <p class="text-light opacity-75 small mb-0">Selesaikan transaksi sebelum batas waktu berakhir</p>
    </div>
</div>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            {{-- Batas Waktu --}}
            <div class="card border rounded-4 text-center p-4 mb-4 bg-white shadow-sm" style="border-color:#E2E8F0!important;">
                <small class="text-muted d-block mb-1">Batas Waktu Pembayaran</small>
                <div class="display-6 fw-bold font-monospace text-dark mb-2" id="timerInstruksi">00:00:00</div>
                <small class="text-muted">Lakukan pembayaran sesuai dengan nominal tagihan di bawah ini.</small>
            </div>

            {{-- Detail Pembayaran --}}
            <div class="card border rounded-4 p-4 mb-4 bg-white shadow-sm" style="border-color:#E2E8F0!important;">
                <div class="text-center pb-3 border-bottom mb-4">
                    <span class="badge bg-light text-dark border px-3 py-1 rounded-pill mb-2">{{ $paymentInfo['metode'] }}</span>
                    <small class="text-muted d-block mb-1">Total Tagihan Pembayaran</small>
                    <h2 class="fw-bold text-success mb-0">Rp {{ number_format($paymentInfo['jumlah_bayar'], 0, ',', '.') }}</h2>
                </div>

                @if(str_contains(strtolower($paymentInfo['metode']), 'qris'))
                    {{-- Tampilan QRIS --}}
                    <div class="text-center py-3">
                        <p class="fw-semibold text-dark mb-3">Scan QRIS ini menggunakan aplikasi e-wallet atau M-Banking Anda:</p>
                        <div class="p-3 bg-white border rounded-4 d-inline-block shadow-sm mb-3">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=GLAMPING_SOLOK_{{ $pemesanan->id_pemesanan }}_{{ $paymentInfo['jumlah_bayar'] }}" width="220" height="220" alt="QRIS Code">
                        </div>
                        <p class="text-muted small">Merchant: <strong>Glamping Danau Kembar Scenery</strong></p>
                    </div>
                @else
                    {{-- Virtual Account --}}
                    <div class="p-3 rounded-4 bg-light border mb-4">
                        <small class="text-muted d-block mb-1">Nomor Virtual Account / Kode Bayar:</small>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <span class="fw-bold fs-3 font-monospace text-dark" id="vaNum">{{ $paymentInfo['va_number'] }}</span>
                            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick="copyVA()">
                                Salin
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Petunjuk Pembayaran --}}
                <h6 class="fw-bold mb-3 text-dark">Tata Cara Pembayaran:</h6>
                <ol class="small text-muted ps-3 mb-0 lh-lg">
                    <li>Buka aplikasi Mobile Banking atau E-Wallet Anda.</li>
                    <li>Pilih menu <strong>Transfer / Pembayaran</strong> &rarr; <strong>Virtual Account / QRIS</strong>.</li>
                    <li>Masukkan nomor VA: <strong class="text-dark">{{ $paymentInfo['va_number'] }}</strong> atau lakukan scan kode QR.</li>
                    <li>Pastikan total nominal tagihan tepat sebesar <strong class="text-dark">Rp {{ number_format($paymentInfo['jumlah_bayar'], 0, ',', '.') }}</strong>.</li>
                    <li>Selesaikan transaksi sesuai petunjuk di aplikasi bank Anda.</li>
                </ol>
            </div>

            {{-- Konfirmasi Pembayaran --}}
            <form action="{{ route('pembayaran.konfirmasi', $pemesanan->id_pemesanan) }}" method="POST">
                @csrf
                <div class="card border rounded-4 p-4 text-center bg-white shadow-sm" style="border-color:#E2E8F0!important;">
                    <p class="text-muted small mb-3">Jika Anda telah menyelesaikan transaksi di aplikasi bank/e-wallet, klik tombol konfirmasi di bawah ini.</p>
                    <button type="submit" class="btn text-white rounded-pill py-3 px-5 fw-bold fs-6 shadow-xs" style="background:#387478;">
                        Saya Sudah Bayar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let sisaDetik = {{ $pemesanan->sisa_detik }};
    const timerElem = document.getElementById('timerInstruksi');

    function formatTime(seconds) {
        let h = Math.floor(seconds / 3600);
        let m = Math.floor((seconds % 3600) / 60);
        let s = seconds % 60;
        return [h, m, s].map(v => v < 10 ? '0' + v : v).join(':');
    }

    if (sisaDetik > 0) {
        timerElem.innerText = formatTime(sisaDetik);
        setInterval(() => {
            sisaDetik--;
            if (sisaDetik <= 0) {
                timerElem.innerText = 'EXPIRED';
                alert('Batas waktu pembayaran telah habis.');
                window.location.reload();
            } else {
                timerElem.innerText = formatTime(sisaDetik);
            }
        }, 1000);
    }

    function copyVA() {
        const vaText = document.getElementById('vaNum').innerText;
        navigator.clipboard.writeText(vaText).then(() => {
            alert('Nomor Virtual Account berhasil disalin!');
        });
    }
</script>
@endsection
