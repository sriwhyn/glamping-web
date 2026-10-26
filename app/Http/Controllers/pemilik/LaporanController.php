<?php

namespace App\Http\Controllers\pemilik;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function laporanPemesanan(Request $request)
    {
        $query = Pemesanan::with(['user', 'glamping', 'pembayaran']);

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tanggal_checkin', [$request->tgl_mulai, $request->tgl_selesai]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('status_pelunasan')) {
            $query->where('status_pelunasan', $request->status_pelunasan);
        }

        $pemesanans = $query->latest()->get();

        return view('pemilik.laporan.pemesanan', compact('pemesanans'));
    }

    public function laporanPendapatan(Request $request)
    {
        $query = Pemesanan::with(['user', 'glamping', 'pembayaran'])
            ->whereIn('status', ['Dikonfirmasi', 'Selesai']);

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tanggal_checkin', [$request->tgl_mulai, $request->tgl_selesai]);
        }

        if ($request->filled('status_pelunasan')) {
            $query->where('status_pelunasan', $request->status_pelunasan);
        }

        $pemesanans = $query->latest()->get();

        $totalDP = $pemesanans->sum(function($p) { 
            return $p->pembayaran ? (float) $p->pembayaran->jumlah_bayar : ($p->total_harga * 0.5); 
        });

        $totalPelunasan = $pemesanans->sum(function($p) {
            if ($p->status_pelunasan === 'Lunas') {
                $dpAwal = $p->pembayaran ? (float) $p->pembayaran->jumlah_bayar : ($p->total_harga * 0.5);
                return max(0, $p->total_harga - $dpAwal);
            }
            return 0;
        });

        $totalDendaKerusakan = $pemesanans->sum('denda_kerusakan');
        $totalPendapatan = $totalDP + $totalPelunasan + $totalDendaKerusakan;

        return view('pemilik.laporan.pendapatan', compact(
            'pemesanans',
            'totalPendapatan',
            'totalDP',
            'totalPelunasan',
            'totalDendaKerusakan'
        ));
    }

    public function cetakPemesanan(Request $request)
    {
        $query = Pemesanan::with(['user', 'glamping', 'pembayaran']);

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tanggal_checkin', [$request->tgl_mulai, $request->tgl_selesai]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('status_pelunasan')) {
            $query->where('status_pelunasan', $request->status_pelunasan);
        }

        $pemesanans = $query->latest()->get();

        return view('pemilik.laporan.cetak_pemesanan', compact('pemesanans'));
    }

    public function cetakPendapatan(Request $request)
    {
        $query = Pemesanan::with(['user', 'glamping', 'pembayaran'])
            ->whereIn('status', ['Dikonfirmasi', 'Selesai']);

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tanggal_checkin', [$request->tgl_mulai, $request->tgl_selesai]);
        }

        if ($request->filled('status_pelunasan')) {
            $query->where('status_pelunasan', $request->status_pelunasan);
        }

        $pemesanans = $query->latest()->get();

        $totalDP = $pemesanans->sum(function($p) { 
            return $p->pembayaran ? (float) $p->pembayaran->jumlah_bayar : ($p->total_harga * 0.5); 
        });

        $totalPelunasan = $pemesanans->sum(function($p) {
            if ($p->status_pelunasan === 'Lunas') {
                $dpAwal = $p->pembayaran ? (float) $p->pembayaran->jumlah_bayar : ($p->total_harga * 0.5);
                return max(0, $p->total_harga - $dpAwal);
            }
            return 0;
        });

        $totalDendaKerusakan = $pemesanans->sum('denda_kerusakan');
        $totalPendapatan = $totalDP + $totalPelunasan + $totalDendaKerusakan;

        return view('pemilik.laporan.cetak_pendapatan', compact(
            'pemesanans',
            'totalPendapatan',
            'totalDP',
            'totalPelunasan',
            'totalDendaKerusakan'
        ));
    }
}
