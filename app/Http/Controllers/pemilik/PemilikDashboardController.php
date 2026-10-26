<?php

namespace App\Http\Controllers\pemilik;

use App\Http\Controllers\Controller;
use App\Models\Glamping;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PemilikDashboardController extends Controller
{
    public function index()
    {
        $totalPemesanan = Pemesanan::count();
        $pemesananSelesai = Pemesanan::where('status', 'Selesai')->orWhere('status', 'Dikonfirmasi')->count();
        $totalPendapatan = Pembayaran::where('status_verifikasi', 'Diterima')->sum('jumlah_bayar');

        $pemesananTerbaru = Pemesanan::with(['user', 'glamping', 'pembayaran'])->latest()->take(5)->get();

        return view('pemilik.dashboard', compact(
            'totalPemesanan',
            'pemesananSelesai',
            'totalPendapatan',
            'pemesananTerbaru'
        ));
    }
}
