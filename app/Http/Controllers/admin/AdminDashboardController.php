<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Glamping;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        Pemesanan::cancelExpiredOrders();

        $totalGlamping = Glamping::count();
        $totalPemesanan = Pemesanan::count();
        $totalPengunjung = User::where('role', 'pengunjung')->count();
        $totalPendapatan = Pembayaran::where('status_verifikasi', 'Diterima')->sum('jumlah_bayar');

        $pemesananTerbaru = Pemesanan::with(['user', 'glamping'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalGlamping',
            'totalPemesanan',
            'totalPengunjung',
            'totalPendapatan',
            'pemesananTerbaru'
        ));
    }
}
