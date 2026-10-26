<?php

namespace App\Http\Controllers;

use App\Models\Glamping;
use App\Models\Pemesanan;
use App\Models\Diskon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservasiController extends Controller
{
    // Simpan pemesanan baru
    public function store(Request $request)
    {
        // Batalkan pesanan kadaluarsa
        Pemesanan::cancelExpiredOrders();

        $glamping = Glamping::findOrFail($request->id_glamping);

        // Cek jika pengguna yang sama sudah memiliki pesanan pending yang belum dibayar untuk unit & tanggal ini
        $existingPending = Pemesanan::where('id_user', Auth::id())
            ->where('id_glamping', $request->id_glamping)
            ->where('status', 'Menunggu Pembayaran')
            ->where('tanggal_checkin', $request->tanggal_checkin)
            ->where('created_at', '>=', now()->subHour())
            ->first();

        if ($existingPending) {
            return redirect()->route('pembayaran.create', $existingPending->id_pemesanan)
                ->with('info', 'Anda sudah memiliki pesanan yang menunggu pembayaran untuk tanggal ini. Silakan selesaikan pembayaran Anda.');
        }

        $namaTipe = strtolower($glamping->tipeGlamping->nama_tipe ?? '');
        $namaUnit = strtolower($glamping->nama_glamping);
        $isGlamping = str_contains($namaTipe, 'glamping') || str_contains($namaUnit, 'glamping');
        $isFamilyRoom = str_contains($namaTipe, 'family') || str_contains($namaUnit, 'family');
        $isCabin = !$isGlamping;

        $kapDewasa = (int) ($glamping->kapasitas_dewasa ?? 2);
        $kapAnak   = max(1, (int) ($glamping->kapasitas_anak ?? 1));
        $totalKapasitasUnit = (int) ($glamping->kapasitas ?? 5);
        $stokUnit  = (int) ($glamping->jumlah_unit ?? 1);

        $maxExtraBedPerUnit = 2;
        $extraBedCapacity   = 2;
        $maxTambahanOrang   = $maxExtraBedPerUnit * $extraBedCapacity;

        $jmlDewasa    = (int) $request->jumlah_dewasa;
        $jmlAnak      = (int) $request->input('jumlah_anak', 0);
        $jmlUnitPesan = (int) $request->jumlah_unit_pesan;

        // Hitung unit dibutuhkan
        if ($isFamilyRoom) {
            $totalTamu = $jmlDewasa + $jmlAnak;
            $unitDibutuhkan = max(1, (int) ceil($totalTamu / max(1, $totalKapasitasUnit)));
        } else if ($isCabin) {
            $unitDewasa = max(1, (int) ceil($jmlDewasa / max(1, $kapDewasa)));
            $unitAnak   = $jmlAnak > 0 ? max(1, (int) ceil($jmlAnak / $kapAnak)) : 1;
            $unitDibutuhkan = max($unitDewasa, $unitAnak);
        } else {
            $maxPerUnitDewasa = $kapDewasa + $maxTambahanOrang;
            $unitDewasa = max(1, (int) ceil($jmlDewasa / $maxPerUnitDewasa));
            $unitAnak   = $jmlAnak > 0 ? max(1, (int) ceil($jmlAnak / $kapAnak)) : 1;
            $unitDibutuhkan = max($unitDewasa, $unitAnak);
        }

        $jmlUnitPesan = max($jmlUnitPesan, $unitDibutuhkan);

        // Cek stok unit
        if ($jmlUnitPesan > $stokUnit) {
            return back()->with('error', 'Maaf, stok hanya tersisa ' . $stokUnit . ' unit. Tidak cukup untuk jumlah tamu yang diinput.');
        }

        // Hitung kapasitas
        $baseDewasa = $kapDewasa * $jmlUnitPesan;
        $maxAnak    = $kapAnak * $jmlUnitPesan;

        if ($isCabin) {
            $maxDewasa = $baseDewasa;
        } else {
            $maxDewasa = $baseDewasa + ($maxTambahanOrang * $jmlUnitPesan);
        }

        // Validasi jam check-in hari ini
        $todayDate = date('Y-m-d');
        if ($request->tanggal_checkin == $todayDate) {
            $currentHour = (int)date('H');
            if ($currentHour >= 23) {
                return back()->with('error', 'Batas jam pemesanan untuk hari ini (23:00 WIB) sudah lewat. Silakan pilih tanggal check-in mulai besok.');
            }
            if ($request->jam_checkin) {
                $requestedHour = (int) substr($request->jam_checkin, 0, 2);
                if ($requestedHour <= $currentHour) {
                    return back()->with('error', 'Jam check-in (' . $request->jam_checkin . ') untuk hari ini sudah lewat. Silakan pilih jam yang akan datang.');
                }
            }
        }

        // Validasi input
        $request->validate([
            'id_glamping'      => 'required|exists:glamping,id_glamping',
            'tanggal_checkin'   => 'required|date|after_or_equal:today',
            'jam_checkin'       => 'nullable|string',
            'tanggal_checkout'  => 'required|date|after:tanggal_checkin',
            'jumlah_dewasa'     => 'required|integer|min:1|max:' . $maxDewasa,
            'jumlah_anak'       => 'nullable|integer|min:0|max:' . $maxAnak,
            'kode_diskon'       => 'nullable|string',
            'kode_promo'        => 'nullable|string',
        ], [
            'jumlah_dewasa.max' => 'Jumlah dewasa maksimal ' . $maxDewasa . ' orang untuk ' . $jmlUnitPesan . ' unit.',
            'jumlah_anak.max'   => 'Jumlah anak maksimal ' . $maxAnak . ' anak untuk ' . $jmlUnitPesan . ' unit.',
        ]);

        // Cek ketersediaan unit
        $unitTerpakai = Pemesanan::where('id_glamping', $request->id_glamping)
            ->where('status', 'Dikonfirmasi')
            ->where('tanggal_checkin', '<', $request->tanggal_checkout)
            ->where('tanggal_checkout', '>', $request->tanggal_checkin)
            ->sum('jumlah_unit_pesan');

        $sisaUnit = max(0, $stokUnit - $unitTerpakai);

        if ($jmlUnitPesan > $sisaUnit) {
            return back()->with('error', 'Maaf, hanya tersisa ' . $sisaUnit . ' unit kamar untuk tanggal tersebut.');
        }

        // Hitung durasi menginap
        $start = new \DateTime($request->tanggal_checkin);
        $end   = new \DateTime($request->tanggal_checkout);
        $malam = max(1, $start->diff($end)->days);

        // Hitung sewa kamar
        $totalHarga = $malam * $glamping->harga * $jmlUnitPesan;

        // Hitung ekstra bed
        $jmlExtrabed   = 0;
        $biayaExtrabed = 0;

        if ($isGlamping && $jmlDewasa > $baseDewasa) {
            $extraPeople = $jmlDewasa - $baseDewasa;
            $jmlExtrabed = (int) ceil($extraPeople / $extraBedCapacity);

            $hargaEB = ($glamping->harga_extrabed && $glamping->harga_extrabed > 0)
                ? $glamping->harga_extrabed : 150000;
            $biayaExtrabed = $jmlExtrabed * $hargaEB * $malam;
            $totalHarga += $biayaExtrabed;
        }

        // Hitung diskon
        $idDiskon    = null;
        $pesanDiskon = '';
        $inputKode   = $request->input('kode_diskon', $request->input('kode_promo'));

        if (!empty($inputKode)) {
            $diskon = Diskon::where('kode_diskon', strtoupper(trim($inputKode)))
                ->where('status', 'aktif')
                ->first();

            if ($diskon) {
                $idDiskon    = $diskon->id_diskon;
                $totalHarga  = max(0, $totalHarga - $diskon->nilai_diskon);
                $pesanDiskon = ' Potongan Rp ' . number_format($diskon->nilai_diskon, 0, ',', '.') . ' berhasil diterapkan!';
            } else {
                return back()->with('error', 'Kode diskon tidak valid atau sudah tidak aktif.');
            }
        }

        $jamCheckin = $request->input('jam_checkin', '14:00 WIB');
        if (!str_contains($jamCheckin, 'WIB')) {
            $jamCheckin .= ' WIB';
        }

        // Simpan pemesanan
        $pemesanan = Pemesanan::create([
            'id_user'          => Auth::id(),
            'id_glamping'      => $glamping->id_glamping,
            'id_diskon'        => $idDiskon,
            'tanggal_checkin'  => $request->tanggal_checkin,
            'jam_checkin'      => $jamCheckin,
            'tanggal_checkout' => $request->tanggal_checkout,
            'jumlah_tamu'      => $jmlDewasa + $jmlAnak,
            'jumlah_dewasa'    => $jmlDewasa,
            'jumlah_anak'      => $jmlAnak,
            'jumlah_unit_pesan'=> $jmlUnitPesan,
            'jumlah_extrabed'  => $jmlExtrabed,
            'biaya_extrabed'   => $biayaExtrabed,
            'tipe_pembayaran'  => 'dp',
            'total_harga'      => $totalHarga,
            'status'           => 'Menunggu Pembayaran',
            'reschedule_count' => 0,
        ]);

        \Illuminate\Support\Facades\Log::info("Pemesanan Baru dibuat: ID #{$pemesanan->id_pemesanan} | Pengunjung: " . Auth::user()->nama . " | Unit: {$glamping->nama_glamping} | Check-in: {$pemesanan->tanggal_checkin}");

        return redirect()->route('pembayaran.create', $pemesanan->id_pemesanan)
            ->with('success', 'Pemesanan berhasil dibuat!' . $pesanDiskon . ' Silakan lakukan pembayaran DP 50% dalam waktu 1 jam.');
    }

    // Reschedule pemesanan
    public function reschedule(Request $request, $id_pemesanan)
    {
        Pemesanan::cancelExpiredOrders();

        $pemesanan = Pemesanan::where('id_user', Auth::id())->findOrFail($id_pemesanan);

        if (!in_array($pemesanan->status, ['Dikonfirmasi', 'Menunggu Verifikasi'])) {
            return back()->with('error', 'Reschedule hanya dapat dilakukan untuk pesanan yang aktif (Dikonfirmasi atau Menunggu Verifikasi).');
        }

        if ($pemesanan->reschedule_count >= 1) {
            return back()->with('error', 'Maaf, Anda telah melakukan reschedule 1 kali. Reschedule hanya diizinkan maksimal 1 kali per pemesanan.');
        }

        $glamping = Glamping::findOrFail($pemesanan->id_glamping);
        $stokUnit = (int) ($glamping->jumlah_unit ?? 1);

        $request->validate([
            'tanggal_checkin'   => 'required|date|after_or_equal:today',
            'tanggal_checkout'  => 'required|date|after:tanggal_checkin',
            'jumlah_unit_pesan' => 'nullable|integer|min:1|max:' . $stokUnit,
        ], [
            'tanggal_checkin.after_or_equal' => 'Tanggal check-in baru minimal adalah hari ini.',
            'tanggal_checkout.after'        => 'Tanggal check-out harus setelah tanggal check-in.',
            'jumlah_unit_pesan.min'         => 'Jumlah unit kamar minimal 1.',
            'jumlah_unit_pesan.max'         => 'Jumlah unit kamar maksimal ' . $stokUnit . ' unit sesuai ketersediaan tipe ini.',
        ]);

        $jmlUnitBaru = (int) $request->input('jumlah_unit_pesan', $pemesanan->jumlah_unit_pesan ?? 1);
        if ($jmlUnitBaru < 1) {
            $jmlUnitBaru = 1;
        }

        // Cek ketersediaan tanggal baru
        $unitTerpakai = Pemesanan::where('id_glamping', $pemesanan->id_glamping)
            ->where('id_pemesanan', '!=', $pemesanan->id_pemesanan)
            ->whereIn('status', ['Dikonfirmasi', 'Menunggu Verifikasi'])
            ->where('tanggal_checkin', '<', $request->tanggal_checkout)
            ->where('tanggal_checkout', '>', $request->tanggal_checkin)
            ->sum('jumlah_unit_pesan');

        $sisaUnit = max(0, $stokUnit - $unitTerpakai);

        if ($jmlUnitBaru > $sisaUnit) {
            return back()->with('error', 'Maaf, hanya tersisa ' . $sisaUnit . ' unit kamar untuk tanggal baru yang dipilih. Tidak cukup untuk permintaan Anda (' . $jmlUnitBaru . ' unit).');
        }

        // Hitung durasi menginap baru
        $start = new \DateTime($request->tanggal_checkin);
        $end   = new \DateTime($request->tanggal_checkout);
        $malamBaru = max(1, $start->diff($end)->days);

        // Hitung sewa kamar baru
        $totalHargaBaru = $malamBaru * $glamping->harga * $jmlUnitBaru;

        // Hitung ulang biaya extrabed
        $biayaExtrabedBaru = 0;
        if ($pemesanan->jumlah_extrabed > 0) {
            $hargaEB = ($glamping->harga_extrabed && $glamping->harga_extrabed > 0)
                ? $glamping->harga_extrabed : 150000;
            $biayaExtrabedBaru = $pemesanan->jumlah_extrabed * $hargaEB * $malamBaru;
            $totalHargaBaru += $biayaExtrabedBaru;
        }

        // Terapkan diskon jika ada
        if ($pemesanan->diskon) {
            $totalHargaBaru = max(0, $totalHargaBaru - $pemesanan->diskon->nilai_diskon);
        }

        $totalHargaLama = $pemesanan->total_harga;
        $selisih = $totalHargaBaru - $totalHargaLama;

        // Update tanggal menginap, jumlah unit, total harga, dan trigger notifikasi admin
        $pemesanan->update([
            'tanggal_checkin'        => $request->tanggal_checkin,
            'tanggal_checkout'       => $request->tanggal_checkout,
            'jumlah_unit_pesan'      => $jmlUnitBaru,
            'biaya_extrabed'         => $biayaExtrabedBaru,
            'total_harga'            => $totalHargaBaru,
            'reschedule_count'       => $pemesanan->reschedule_count + 1,
            'reschedule_at'          => now(),
            'is_reschedule_notified' => 0,
        ]);

        $infoHarga = '';
        if ($selisih > 0) {
            $infoHarga = ' Total harga disesuaikan menjadi Rp ' . number_format($totalHargaBaru, 0, ',', '.') . ' (Penambahan Rp ' . number_format($selisih, 0, ',', '.') . ').';
        } elseif ($selisih < 0) {
            $infoHarga = ' Total harga disesuaikan menjadi Rp ' . number_format($totalHargaBaru, 0, ',', '.') . ' (Pengurangan Rp ' . number_format(abs($selisih), 0, ',', '.') . ').';
        } else {
            $infoHarga = ' Total harga tetap Rp ' . number_format($totalHargaBaru, 0, ',', '.') . '.';
        }

        return back()->with('success', 'Jadwal reservasi (' . $jmlUnitBaru . ' unit) berhasil diubah ke tanggal ' . date('d M Y', strtotime($request->tanggal_checkin)) . ' - ' . date('d M Y', strtotime($request->tanggal_checkout)) . '.' . $infoHarga . ' Reschedule telah digunakan (1/1).');
    }

    // Hapus riwayat dibatalkan
    public function destroy($id_pemesanan)
    {
        $pemesanan = Pemesanan::where('id_user', Auth::id())->findOrFail($id_pemesanan);

        if ($pemesanan->status != 'Dibatalkan') {
            return back()->with('error', 'Hanya riwayat pesanan yang dibatalkan atau kadaluarsa yang dapat dihapus.');
        }

        if ($pemesanan->pembayaran) {
            $pemesanan->pembayaran()->delete();
        }

        $pemesanan->delete();

        return back()->with('success', 'Riwayat pemesanan yang dibatalkan berhasil dihapus.');
    }

    // Hapus semua riwayat dibatalkan
    public function destroyAllCancelled()
    {
        $pemesanans = Pemesanan::where('id_user', Auth::id())
            ->where('status', 'Dibatalkan')
            ->get();

        $count = $pemesanans->count();

        if ($count == 0) {
            return back()->with('error', 'Tidak ada riwayat pemesanan dibatalkan yang perlu dibersihkan.');
        }

        foreach ($pemesanans as $p) {
            if ($p->pembayaran) {
                $p->pembayaran()->delete();
            }
            $p->delete();
        }

        return back()->with('success', $count . ' riwayat pemesanan yang dibatalkan/expired berhasil dibersihkan.');
    }

    // Cek status & keabsahan kode diskon via AJAX (Tanpa Reload Halaman)
    public function cekDiskon(Request $request)
    {
        $kode = strtoupper(trim($request->input('kode_diskon', '')));

        if (empty($kode)) {
            return response()->json([
                'valid' => false,
                'message' => 'Silakan masukkan kode diskon.'
            ]);
        }

        $diskon = Diskon::where('kode_diskon', $kode)
            ->where('status', 'aktif')
            ->first();

        if ($diskon) {
            return response()->json([
                'valid' => true,
                'nilai_diskon' => (float) $diskon->nilai_diskon,
                'message' => 'Diskon Rp ' . number_format($diskon->nilai_diskon, 0, ',', '.') . ' berhasil diterapkan!'
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Kode diskon tidak valid atau sudah tidak aktif.'
        ]);
    }
}
