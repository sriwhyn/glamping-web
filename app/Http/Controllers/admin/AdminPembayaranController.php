<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Http\Request;

class AdminPembayaranController extends Controller
{
    public function index(Request $request)
    {
        Pemesanan::cancelExpiredOrders();

        $query = Pembayaran::with(['pemesanan.user', 'pemesanan.glamping']);

        // Filter pencarian (ID Pemesanan, Nama Pengunjung, Email, Metode Bayar)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id_pemesanan', 'like', "%{$search}%")
                  ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                  ->orWhereHas('pemesanan.user', function($u) use ($search) {
                      $u->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter status verifikasi
        if ($request->filled('status_verifikasi')) {
            $status = $request->status_verifikasi;
            if ($status == 'Menunggu') {
                $query->whereNotIn('status_verifikasi', ['Diterima', 'Disetujui', 'Ditolak']);
            } elseif ($status == 'Diterima') {
                $query->whereIn('status_verifikasi', ['Diterima', 'Disetujui']);
            } elseif ($status == 'Ditolak') {
                $query->where('status_verifikasi', 'Ditolak');
            }
        }

        $pembayarans = $query->latest()->get();

        // Hitung statistik ringkas untuk card & tabs
        $totalCount = Pembayaran::count();
        $menungguCount = Pembayaran::whereNotIn('status_verifikasi', ['Diterima', 'Disetujui', 'Ditolak'])->count();
        $diterimaCount = Pembayaran::whereIn('status_verifikasi', ['Diterima', 'Disetujui'])->count();
        $ditolakCount = Pembayaran::where('status_verifikasi', 'Ditolak')->count();

        return view('admin.pembayaran.index', compact(
            'pembayarans',
            'totalCount',
            'menungguCount',
            'diterimaCount',
            'ditolakCount'
        ));
    }

    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:Diterima,Ditolak',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);

        // Jika pembayaran online (otomatis), verifikasi sudah terkunci
        if (!$pembayaran->bukti_pembayaran || $pembayaran->bukti_pembayaran == 'images/logo1.jpg') {
            return back()->with('error', 'Pembayaran online sudah terverifikasi otomatis oleh sistem dan tidak dapat diubah.');
        }

        $pembayaran->update([
            'status_verifikasi' => $request->status_verifikasi,
        ]);

        // Update status pemesanan
        if ($request->status_verifikasi == 'Diterima') {
            $pembayaran->pemesanan->update(['status' => 'Dikonfirmasi']);
            
            // Batalkan pemesanan bentrok
            Pemesanan::where('id_glamping', $pembayaran->pemesanan->id_glamping)
                ->where('id_pemesanan', '!=', $pembayaran->pemesanan->id_pemesanan)
                ->where('status', 'Menunggu Pembayaran')
                ->where('tanggal_checkin', '<', $pembayaran->pemesanan->tanggal_checkout)
                ->where('tanggal_checkout', '>', $pembayaran->pemesanan->tanggal_checkin)
                ->update(['status' => 'Dibatalkan']);
        } else {
            // Ketika pembayaran ditolak: JANGAN dibatalkan/dihapus!
            // Kembalikan status pemesanan ke 'Menunggu Pembayaran' dan reset created_at ke waktu sekarang
            // agar pengguna mendapat kesempatan 1 jam baru untuk melakukan pembayaran/upload ulang bukti.
            $pembayaran->pemesanan->update([
                'status' => 'Menunggu Pembayaran',
                'created_at' => now(),
            ]);
        }

        return back()->with('success', 'Status verifikasi pembayaran berhasil diupdate.');
    }
}
