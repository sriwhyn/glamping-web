<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Pemesanan - Glamping Danau Kembar</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 11px; margin: 20px; color: #333; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #243642; padding-bottom: 12px; margin-bottom: 20px; }
        .header-logo { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #387478; }
        .header-title { flex-grow: 1; text-align: center; margin: 0 15px; }
        .header-title h2 { margin: 0; color: #243642; font-size: 18px; font-weight: bold; letter-spacing: 0.5px; }
        .header-title p { margin: 3px 0 0; color: #387478; font-weight: 600; font-size: 13px; }
        .header-title small { color: #666; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #CBD5E1; padding: 8px 10px; text-align: left; }
        th { background-color: #F1F5F9; font-weight: bold; color: #1E293B; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge-status { padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; display: inline-block; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="header">
    <img src="{{ asset('images/logo1.jpg') }}" alt="Logo Glamping" class="header-logo">
    <div class="header-title">
        <h2>GLAMPING DANAU KEMBAR SCENERY</h2>
        <p>LAPORAN TRANSAKSI PEMESANAN GLAMPING</p>
        <small>Tanggal Cetak: {{ date('d/m/Y H:i') }} WIB &bull; Dicetak oleh: {{ Auth::user()->nama }} ({{ ucfirst(Auth::user()->role) }})</small>
    </div>
    <div style="width:60px;"></div>
</div>

<div class="no-print" style="margin-bottom: 15px; text-align: right;">
    <button onclick="window.print()" style="padding: 8px 20px; background: #387478; color: white; border: none; border-radius: 20px; font-weight: bold; cursor: pointer;">
        🖨️ Cetak PDF / Print
    </button>
</div>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width:30px;">No</th>
            <th>ID Pemesanan</th>
            <th>Nama Pengunjung</th>
            <th>Unit Glamping</th>
            <th>Check-in / Check-out</th>
            <th class="text-right">Total Tagihan</th>
            <th class="text-center">Status Pelunasan</th>
            <th class="text-right">Denda Kerusakan</th>
            <th class="text-center">Status Reservasi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pemesanans as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">#{{ $p->id_pemesanan }}</td>
                <td>{{ $p->user->nama ?? '-' }}</td>
                <td>{{ $p->glamping->nama_glamping ?? '-' }}</td>
                <td>{{ date('d/m/Y', strtotime($p->tanggal_checkin)) }} - {{ date('d/m/Y', strtotime($p->tanggal_checkout)) }}</td>
                <td class="text-right" style="font-weight: bold; color: #243642;">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td class="text-center" style="font-weight: bold; color: {{ $p->status_pelunasan == 'Lunas' ? '#166534' : '#B45309' }};">
                    {{ $p->status_pelunasan == 'Lunas' ? 'Lunas' : 'DP (Sisa Rp ' . number_format($p->sisa_bayar, 0, ',', '.') . ')' }}
                </td>
                <td class="text-right" style="color: {{ $p->denda_kerusakan > 0 ? '#991B1B' : '#64748B' }};">
                    {{ $p->denda_kerusakan > 0 ? 'Rp ' . number_format($p->denda_kerusakan, 0, ',', '.') : '-' }}
                </td>
                <td class="text-center">{{ $p->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data pemesanan.</td>
            </tr>
        @endforelse
    </tbody>
    @if($pemesanans->count() > 0)
        <tfoot>
            <tr style="background-color: #F1F5F9; font-weight: bold;">
                <td colspan="5" class="text-right">Total Ringkasan:</td>
                <td class="text-right" style="color: #243642;">Rp {{ number_format($pemesanans->sum('total_harga'), 0, ',', '.') }}</td>
                <td class="text-center" style="color: #166534;">{{ $pemesanans->where('status_pelunasan', 'Lunas')->count() }} Lunas</td>
                <td class="text-right" style="color: #991B1B;">Rp {{ number_format($pemesanans->sum('denda_kerusakan'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    @endif
</table>

<div style="margin-top: 50px; text-align: right;">
    <p>Solok, {{ date('d F Y') }}</p>
    <p style="margin-top: 55px; font-weight: bold;">( {{ Auth::user()->role === 'admin' ? 'Administrator' : 'Pemilik Glamping' }} )</p>
</div>

</body>
</html>
