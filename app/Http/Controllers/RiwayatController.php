<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        // Batalkan pesanan kadaluarsa
        Pemesanan::cancelExpiredOrders();

        $pemesanans = Pemesanan::with(['glamping', 'pembayaran', 'promo'])
            ->where('id_user', Auth::id())
            ->latest()
            ->get();

        return view('user.pemesanan.index', compact('pemesanans'));
    }
}
