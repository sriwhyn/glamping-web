<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;

class AdminPemesananController extends Controller
{
    public function index(Request $request)
    {
        Pemesanan::cancelExpiredOrders();

        $query = Pemesanan::with(['user', 'glamping', 'pembayaran']);

        // Filter berdasarkan pencarian (ID, nama pengunjung, email, atau nama glamping)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id_pemesanan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('glamping', function($g) use ($search) {
                      $g->where('nama_glamping', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan status pemesanan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter reschedule
        if ($request->filled('filter_reschedule')) {
            $query->where('reschedule_count', '>', 0);
            if ($request->filter_reschedule == 'unread') {
                $query->where('is_reschedule_notified', 0);
            }
        }

        // Filter berdasarkan tanggal checkin
        if ($request->filled('tanggal_checkin')) {
            $query->whereDate('tanggal_checkin', $request->tanggal_checkin);
        }

        $pemesanans = $query->latest()->get();

        // Count stats untuk ringkasan di atas tabel
        $totalCount = Pemesanan::count();
        $menungguVerifikasiCount = Pemesanan::where('status', 'Menunggu Verifikasi')->count();
        $dikonfirmasiCount = Pemesanan::where('status', 'Dikonfirmasi')->count();
        $selesaiCount = Pemesanan::where('status', 'Selesai')->count();
        $dibatalkanCount = Pemesanan::where('status', 'Dibatalkan')->count();
        $rescheduleUnreadCount = Pemesanan::where('is_reschedule_notified', 0)->where('reschedule_count', '>', 0)->count();

        return view('admin.pemesanan.index', compact(
            'pemesanans',
            'totalCount',
            'menungguVerifikasiCount',
            'dikonfirmasiCount',
            'selesaiCount',
            'dibatalkanCount',
            'rescheduleUnreadCount'
        ));
    }

    public function show($id)
    {
        $pemesanan = Pemesanan::with(['user', 'glamping', 'pembayaran'])->findOrFail($id);
        
        // Tandai notifikasi reschedule dibaca jika admin membuka detail
        if ($pemesanan->is_reschedule_notified == 0) {
            $pemesanan->update(['is_reschedule_notified' => 1]);
        }

        return view('admin.pemesanan.show', compact('pemesanan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'            => 'nullable|in:Menunggu Pembayaran,Menunggu Verifikasi,Dikonfirmasi,Selesai,Dibatalkan',
            'status_pelunasan'  => 'nullable|in:Belum Lunas,Lunas',
            'metode_pelunasan'  => 'nullable|in:Tunai (Cash),Transfer Bank',
            'jenis_deposit'     => 'nullable|in:KTP,Uang Tunai',
            'status_deposit'    => 'nullable|in:Belum Diserahkan,Ditahan,Dikembalikan,Dipotong Kerusakan',
            'nominal_deposit'   => 'nullable|numeric|min:0',
            'denda_kerusakan'   => 'nullable|numeric|min:0',
            'catatan_kerusakan' => 'nullable|string',
        ]);

        $pemesanan = Pemesanan::with('pembayaran')->findOrFail($id);

        $updateData = [];

        if ($request->filled('status')) {
            // Jika pembayaran online (otomatis), status transaksi terkunci
            if (!($pemesanan->pembayaran && (!$pemesanan->pembayaran->bukti_pembayaran || $pemesanan->pembayaran->bukti_pembayaran == 'images/logo1.jpg'))) {
                $updateData['status'] = $request->status;
            }
        }

        // Kunci status pelunasan jika sudah Lunas
        if ($pemesanan->status_pelunasan !== 'Lunas') {
            if ($request->has('status_pelunasan')) {
                $updateData['status_pelunasan'] = $request->status_pelunasan;
            }

            if ($request->has('metode_pelunasan')) {
                $updateData['metode_pelunasan'] = $request->metode_pelunasan;
            }

            // Jika admin menandai Lunas secara manual (misal Tunai di resepsionis)
            if ($request->status_pelunasan === 'Lunas') {
                $metode = $request->metode_pelunasan ?? 'Tunai (Cash)';
                $sisaBayar = $pemesanan->sisa_bayar > 0 ? $pemesanan->sisa_bayar : ($pemesanan->total_harga - $pemesanan->dibayar);
                
                if ($sisaBayar > 0) {
                    \App\Models\Pembayaran::create([
                        'id_pemesanan'       => $pemesanan->id_pemesanan,
                        'metode_pembayaran'  => $metode . ' - Pelunasan',
                        'jumlah_bayar'       => $sisaBayar,
                        'tanggal_pembayaran' => now(),
                        'status_verifikasi'  => 'Diterima',
                        'bukti_pembayaran'   => 'images/logo1.jpg',
                    ]);
                }
            }
        }

        if ($request->has('jenis_deposit')) {
            $updateData['jenis_deposit'] = $request->jenis_deposit;
        }

        if ($request->has('status_deposit')) {
            $updateData['status_deposit'] = $request->status_deposit;

            // Jika KTP dikembalikan, berarti denda kerusakan (jika ada) telah diselesaikan di resepsionis
            if ($request->status_deposit === 'Dikembalikan') {
                $denda = (float) ($request->denda_kerusakan ?? $pemesanan->denda_kerusakan);
                $catatan = $request->catatan_kerusakan ?? $pemesanan->catatan_kerusakan;
                
                if ($denda > 0 && !str_contains($catatan ?? '', 'Lunas')) {
                    $updateData['catatan_kerusakan'] = trim(($catatan ? $catatan . ' ' : '') . '(Lunas di Resepsionis)');
                    
                    \App\Models\Pembayaran::create([
                        'id_pemesanan'       => $pemesanan->id_pemesanan,
                        'metode_pembayaran'  => 'Tunai (Cash) - Ganti Rugi Fasilitas',
                        'jumlah_bayar'       => $denda,
                        'tanggal_pembayaran' => now(),
                        'status_verifikasi'  => 'Diterima',
                        'bukti_pembayaran'   => 'images/logo1.jpg',
                    ]);
                }
            }
        }

        if ($request->has('nominal_deposit')) {
            $updateData['nominal_deposit'] = (float) $request->nominal_deposit;
        }

        if ($request->has('denda_kerusakan')) {
            $updateData['denda_kerusakan'] = (float) $request->denda_kerusakan;
        }

        if ($request->has('catatan_kerusakan') && !isset($updateData['catatan_kerusakan'])) {
            $updateData['catatan_kerusakan'] = $request->catatan_kerusakan;
        }

        if (!empty($updateData)) {
            $pemesanan->update($updateData);
        }

        return back()->with('success', 'Status & rincian transaksi berhasil diperbarui.');
    }

    public function markRescheduleNotified($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->update(['is_reschedule_notified' => 1]);

        return back()->with('success', 'Notifikasi reschedule berhasil ditandai dibaca.');
    }

    public function destroy($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);

        // Hapus pembayaran terkait
        if ($pemesanan->pembayaran) {
            $pemesanan->pembayaran()->delete();
        }

        $pemesanan->delete();

        return back()->with('success', 'Data pemesanan berhasil dihapus.');
    }
}
