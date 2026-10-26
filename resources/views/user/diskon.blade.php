@extends('layouts.user')

@section('title', 'Kupon Diskon')

@section('content')
<div class="py-4 bg-light border-bottom">
    <div class="container text-center">
        <h2 class="fw-bold mb-2 text-dark">Voucher & Kode Potongan Harga</h2>
        <p class="text-muted mb-0">Gunakan kode diskon aktif di bawah ini saat melakukan pemesanan unit glamping.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 mb-4">
        @forelse($diskons as $d)
            <div class="col-md-6">
                <div class="card border rounded-3 p-4 bg-white h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-danger px-3 py-2 fs-6 mb-2">POTONGAN Rp {{ number_format($d->nilai_diskon, 0, ',', '.') }}</span>
                            <h5 class="fw-bold text-dark mb-1">Voucher Diskon Glamping</h5>
                        </div>
                    </div>

                    <div class="p-3 bg-light border rounded-3 d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <small class="text-muted d-block">Kode Diskon:</small>
                            <strong class="font-monospace fs-5 text-dark">{{ $d->kode_diskon }}</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-dark px-3 py-1" onclick="copyCode('{{ $d->kode_diskon }}', this)">
                            Salin Kode
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Aktif</span>
                        <a href="{{ route('glamping.index') }}" class="btn text-white btn-sm px-3 py-2 fw-semibold" style="background:#387478;">Gunakan Diskon</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted mb-2">Saat ini belum ada kode diskon aktif.</p>
                <a href="{{ route('glamping.index') }}" class="btn text-white px-4 py-2" style="background:#387478;">Lihat Unit Glamping</a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('js')
<script>
function copyCode(code, btnElement) {
    navigator.clipboard.writeText(code).then(function() {
        const originalHTML = btnElement.innerHTML;
        btnElement.innerHTML = 'Tersalin!';
        btnElement.classList.remove('btn-outline-dark');
        btnElement.classList.add('btn-success', 'text-white');
        setTimeout(() => {
            btnElement.innerHTML = originalHTML;
            btnElement.classList.remove('btn-success', 'text-white');
            btnElement.classList.add('btn-outline-dark');
        }, 2000);
    });
}
</script>
@endsection
