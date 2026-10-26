<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Pendapatan - Glamping Danau Kembar</title>
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
        .total-box { margin-top: 20px; padding: 12px 15px; background: #E2F1E7; border-radius: 8px; font-size: 14px; font-weight: bold; text-align: right; color: #243642; border: 1px solid #629584; }
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
        <p>LAPORAN PENDAPATAN TERVERIFIKASI</p>
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
            <th>Pengunjung</th>
            <th>Unit Glamping</th>
            <th class="text-right">Total Tagihan</th>
            <th class="text-right">DP (50%)</th>
            <th class="text-right">Pelunasan (50%)</th>
            <th class="text-right">Denda Kerusakan</th>
            <th class="text-right">Total Pemasukan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pemesanans as $index => $p)
            @php
                $dp = $p->pembayaran ? (float) $p->pembayaran->jumlah_bayar : ($p->total_harga * 0.5);
                $pelunasan = ($p->status_pelunasan == 'Lunas') ? max(0, $p->total_harga - $dp) : 0;
                $denda = $p->denda_kerusakan ?? 0;
                $totalRow = $dp + $pelunasan + $denda;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">#{{ $p->id_pemesanan }}</td>
                <td>{{ $p->user->nama ?? '-' }}</td>
                <td>{{ $p->glamping->nama_glamping ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #166534;">Rp {{ number_format($dp, 0, ',', '.') }}</td>
                <td class="text-right" style="color: {{ $pelunasan > 0 ? '#166534' : '#64748B' }};">
                    {{ $pelunasan > 0 ? 'Rp ' . number_format($pelunasan, 0, ',', '.') : 'Belum Lunas' }}
                </td>
                <td class="text-right" style="color: {{ $denda > 0 ? '#991B1B' : '#64748B' }};">
                    {{ $denda > 0 ? 'Rp ' . number_format($denda, 0, ',', '.') : '-' }}
                </td>
                <td class="text-right" style="font-weight: bold; color: #387478;">Rp {{ number_format($totalRow, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data pendapatan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="total-box">
    <div>Rincian Pendapatan: DP (Rp {{ number_format($totalDP ?? 0, 0, ',', '.') }}) + Pelunasan (Rp {{ number_format($totalPelunasan ?? 0, 0, ',', '.') }}) + Denda (Rp {{ number_format($totalDendaKerusakan ?? 0, 0, ',', '.') }})</div>
    <div style="font-size: 16px; margin-top: 5px;">Total Keseluruhan Pendapatan: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
</div>

<div style="margin-top: 40px; text-align: right;">
    <p>Solok, {{ date('d F Y') }}</p>
    <p style="margin-top: 55px; font-weight: bold;">( {{ Auth::user()->role === 'admin' ? 'Administrator' : 'Pemilik Glamping' }} )</p>
</div>

</body>
</html>
