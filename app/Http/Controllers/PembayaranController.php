<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    private function checkAlreadyBookedByOther($pemesanan)
    {
        $glamping = $pemesanan->glamping;
        if (!$glamping) {
            return false;
        }

        $stokTotal = (int) ($glamping->jumlah_unit ?? 1);

        $unitTerpakai = Pemesanan::where('id_glamping', $pemesanan->id_glamping)
            ->where('id_pemesanan', '!=', $pemesanan->id_pemesanan)
            ->whereIn('status', ['Dikonfirmasi', 'Menunggu Verifikasi', 'Selesai'])
            ->where('tanggal_checkin', '<', $pemesanan->tanggal_checkout)
            ->where('tanggal_checkout', '>', $pemesanan->tanggal_checkin)
            ->sum('jumlah_unit_pesan');

        $sisaUnit = max(0, $stokTotal - $unitTerpakai);

        return ($pemesanan->jumlah_unit_pesan > $sisaUnit);
    }

    public function create($id_pemesanan)
    {
        Pemesanan::cancelExpiredOrders();

        $pemesanan = Pemesanan::with(['glamping', 'diskon', 'promo'])
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        if ($pemesanan->status == 'Dibatalkan') {
            return redirect()->route('riwayat.index')
                ->with('error', 'Waktu pembayaran (1 jam) telah habis. Pemesanan ini otomatis dibatalkan.');
        }

        if ($pemesanan->status == 'Dikonfirmasi') {
            return redirect()->route('pembayaran.sukses', $pemesanan->id_pemesanan);
        }

        // Cek ketersediaan tanggal
        if ($this->checkAlreadyBookedByOther($pemesanan)) {
            $pemesanan->update(['status' => 'Dibatalkan']);
            return redirect()->route('riwayat.index')
                ->with('error', 'Mohon maaf, unit glamping untuk tanggal ini sudah penuh dipesan pengunjung lain.');
        }

        // Token pembayaran DP 50%
        $snapTokenDP = null;
        try {
            $snapTokenDP = $this->generateSnapToken($pemesanan, 'dp');
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
        }

        return view('user.pembayaran.create', compact('pemesanan', 'snapTokenDP'));
    }

    public function batalkan(Request $request, $id_pemesanan)
    {
        $pemesanan = Pemesanan::where('id_user', Auth::id())->findOrFail($id_pemesanan);

        if ($pemesanan->status == 'Menunggu Pembayaran') {
            $pemesanan->update(['status' => 'Dibatalkan']);
            return redirect()->back()->with('success', 'Pemesanan berhasil dibatalkan.');
        }

        if (in_array($pemesanan->status, ['Menunggu Verifikasi', 'Dikonfirmasi'])) {
            $pemesanan->update(['status' => 'Dibatalkan']);
            return redirect()->back()->with('success', 'Pemesanan dibatalkan. Sesuai ketentuan, DP 50% yang telah dibayarkan tidak dapat dikembalikan.');
        }

        return redirect()->back()->with('error', 'Pemesanan ini tidak dapat dibatalkan.');
    }

    public function getSnapTokenAjax(Request $request, $id_pemesanan)
    {
        Pemesanan::cancelExpiredOrders();

        $pemesanan = Pemesanan::where('id_user', Auth::id())->findOrFail($id_pemesanan);

        if ($pemesanan->status == 'Dibatalkan') {
            return response()->json(['error' => 'Waktu pembayaran (1 jam) telah habis.'], 400);
        }

        if ($this->checkAlreadyBookedByOther($pemesanan)) {
            $pemesanan->update(['status' => 'Dibatalkan']);
            return response()->json([
                'error' => 'Mohon maaf, unit glamping untuk tanggal ini sudah penuh dipesan pengunjung lain.'
            ], 400);
        }

        try {
            $snapToken = $this->generateSnapToken($pemesanan, 'dp');
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSnapTokenPelunasan(Request $request, $id_pemesanan)
    {
        $pemesanan = Pemesanan::with(['glamping', 'user'])
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        if ($pemesanan->status == 'Dibatalkan') {
            return response()->json(['error' => 'Pemesanan ini telah dibatalkan.'], 400);
        }

        if ($pemesanan->status_pelunasan == 'Lunas' || $pemesanan->sisa_bayar <= 0) {
            return response()->json(['error' => 'Pemesanan ini sudah lunas 100%.'], 400);
        }

        try {
            $snapToken = $this->generateSnapToken($pemesanan, 'pelunasan');
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function konfirmasiPelunasanMidtrans(Request $request, $id_pemesanan)
    {
        $pemesanan = Pemesanan::where('id_user', Auth::id())->findOrFail($id_pemesanan);

        if ($pemesanan->status_pelunasan == 'Lunas') {
            return response()->json(['success' => true, 'message' => 'Sudah Lunas']);
        }

        $sisaBayar = $pemesanan->sisa_bayar > 0 ? $pemesanan->sisa_bayar : ($pemesanan->total_harga - $pemesanan->dibayar);

        $pemesanan->update([
            'status_pelunasan' => 'Lunas',
            'metode_pelunasan' => 'Transfer Bank (Midtrans)'
        ]);

        if ($sisaBayar > 0) {
            Pembayaran::create([
                'id_pemesanan'       => $pemesanan->id_pemesanan,
                'tanggal_pembayaran' => now()->toDateString(),
                'jumlah_bayar'       => (float) $sisaBayar,
                'metode_pembayaran'  => 'Transfer Bank (Pelunasan Online)',
                'bukti_pembayaran'   => 'images/logo1.jpg',
                'snap_token'         => $request->order_id ?? null,
                'status_verifikasi'  => 'Diterima',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pelunasan berhasil dikonfirmasi!'
        ]);
    }

    public function getSnapTokenDenda(Request $request, $id_pemesanan)
    {
        $pemesanan = Pemesanan::with(['glamping', 'user'])
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        if ($pemesanan->denda_kerusakan <= 0) {
            return response()->json(['error' => 'Tidak ada denda kerusakan pada reservasi ini.'], 400);
        }

        try {
            $snapToken = $this->generateSnapToken($pemesanan, 'denda');
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function konfirmasiDendaMidtrans(Request $request, $id_pemesanan)
    {
        $pemesanan = Pemesanan::where('id_user', Auth::id())->findOrFail($id_pemesanan);

        $catatan = $pemesanan->catatan_kerusakan ?: 'Denda kerusakan';
        if (!str_contains($catatan, 'Lunas')) {
            $catatan .= ' (Lunas Online)';
        }

        $pemesanan->update([
            'status_deposit' => 'Dikembalikan',
            'catatan_kerusakan' => $catatan
        ]);

        // Simpan catatan transaksi pembayaran denda ke tabel pembayaran
        Pembayaran::create([
            'id_pemesanan'       => $pemesanan->id_pemesanan,
            'tanggal_pembayaran' => now()->toDateString(),
            'jumlah_bayar'       => (float) $pemesanan->denda_kerusakan,
            'metode_pembayaran'  => 'Transfer Bank (Denda Kerusakan)',
            'bukti_pembayaran'   => 'images/logo1.jpg',
            'snap_token'         => $request->order_id ?? null,
            'status_verifikasi'  => 'Diterima',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran denda kerusakan berhasil dikonfirmasi!'
        ]);
    }

    private function generateSnapToken($pemesanan, $tipeBayar = 'dp')
    {
        try {
            $serverKey = config('midtrans.serverKey', config('midtrans.serveyKey'));
            if (!$serverKey) {
                return null;
            }

            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = config('midtrans.isProduction', false);
            \Midtrans\Config::$isSanitized = config('midtrans.isSanitized', true);
            \Midtrans\Config::$is3ds = config('midtrans.is3ds', true);
            \Midtrans\Config::$curlOptions[CURLOPT_HTTPHEADER] = [];
            \Midtrans\Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            \Midtrans\Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;

            if ($tipeBayar === 'denda') {
                $jumlahBayar = (int) $pemesanan->denda_kerusakan;
                $orderId = 'TRX-' . $pemesanan->id_pemesanan . '-DENDA-' . time() . '-' . rand(100, 999);
                $itemName = 'Denda Kerusakan Glamping ' . ($pemesanan->glamping->nama_glamping ?? 'Unit');
            } elseif ($tipeBayar === 'pelunasan') {
                $jumlahBayar = $pemesanan->sisa_bayar > 0 ? $pemesanan->sisa_bayar : ($pemesanan->total_harga * 0.5);
                $orderId = 'TRX-' . $pemesanan->id_pemesanan . '-LUNAS-' . time() . '-' . rand(100, 999);
                $itemName = 'Pelunasan Glamping ' . ($pemesanan->glamping->nama_glamping ?? 'Unit') . ' (100%)';
            } else {
                $jumlahBayar = $pemesanan->total_harga * 0.5;
                $orderId = 'TRX-' . $pemesanan->id_pemesanan . '-DP-' . time() . '-' . rand(100, 999);
                $itemName = 'Glamping ' . ($pemesanan->glamping->nama_glamping ?? 'Unit') . ' (DP 50%)';
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $jumlahBayar,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->nama ?? Auth::user()->name ?? 'Pengunjung',
                    'email' => Auth::user()->email ?? 'user@example.com',
                ],
                'item_details' => [
                    [
                        'id' => 'GLAMPING-' . $pemesanan->id_glamping,
                        'price' => (int) $jumlahBayar,
                        'quantity' => 1,
                        'name' => $itemName
                    ]
                ]
            ];

            return \Midtrans\Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token generation error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    public function prosesPembayaran(Request $request, $id_pemesanan)
    {
        Pemesanan::cancelExpiredOrders();

        $pemesanan = Pemesanan::where('id_user', Auth::id())->findOrFail($id_pemesanan);

        if ($pemesanan->status == 'Dibatalkan') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Waktu pembayaran 1 jam telah kadaluarsa!'], 400);
            }
            return redirect()->route('riwayat.index')->with('error', 'Waktu pembayaran 1 jam telah kadaluarsa!');
        }

        if ($this->checkAlreadyBookedByOther($pemesanan)) {
            $pemesanan->update(['status' => 'Dibatalkan']);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Maaf! Unit glamping ini baru saja dipesan & dibayar oleh pengunjung lain untuk tanggal yang sama.'], 400);
            }
            return redirect()->route('riwayat.index')->with('error', 'Maaf! Unit glamping ini baru saja dipesan & dibayar oleh pengunjung lain untuk tanggal yang sama.');
        }

        $metode = $request->input('metode_pembayaran', 'Midtrans Instant Payment');
        $jumlahBayar = $pemesanan->total_harga * 0.5;

        try {
            $snapToken = $this->generateSnapToken($pemesanan, 'dp');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'snap_token' => $snapToken,
                ]);
            }

            $vaNumber = '88390' . sprintf('%08d', Auth::id()) . sprintf('%03d', $pemesanan->id_pemesanan);
            session([
                'payment_info_' . $id_pemesanan => [
                    'metode' => $metode,
                    'tipe_bayar' => 'dp',
                    'jumlah_bayar' => $jumlahBayar,
                    'va_number' => $vaNumber,
                    'snap_token' => $snapToken
                ]
            ]);

            return redirect()->route('pembayaran.instruksi', $id_pemesanan);
        } catch (\Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Gagal memproses ke Midtrans: ' . $e->getMessage()], 500);
            }
            return redirect()->route('pembayaran.instruksi', $id_pemesanan);
        }
    }

    public function instruksi($id_pemesanan)
    {
        Pemesanan::cancelExpiredOrders();

        $pemesanan = Pemesanan::with(['glamping', 'diskon', 'promo'])
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        if ($pemesanan->status == 'Dibatalkan') {
            return redirect()->route('riwayat.index')
                ->with('error', 'Waktu pembayaran reservasi telah kadaluarsa.');
        }

        if ($pemesanan->status == 'Dikonfirmasi') {
            return redirect()->route('pembayaran.sukses', $pemesanan->id_pemesanan);
        }

        $paymentInfo = session('payment_info_' . $id_pemesanan, [
            'metode' => 'Midtrans Payment Gateway',
            'tipe_bayar' => 'dp',
            'jumlah_bayar' => $pemesanan->total_harga * 0.5,
            'va_number' => '88390' . sprintf('%08d', Auth::id()) . sprintf('%03d', $pemesanan->id_pemesanan),
        ]);

        return view('user.pembayaran.instruksi', compact('pemesanan', 'paymentInfo'));
    }

    public function konfirmasiPay(Request $request, $id_pemesanan)
    {
        Pemesanan::cancelExpiredOrders();

        $pemesanan = Pemesanan::where('id_user', Auth::id())->findOrFail($id_pemesanan);

        if ($pemesanan->status == 'Dibatalkan') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Waktu pembayaran 1 jam telah habis!'], 400);
            }
            return redirect()->route('riwayat.index')->with('error', 'Waktu pembayaran 1 jam telah habis!');
        }

        if ($this->checkAlreadyBookedByOther($pemesanan)) {
            $pemesanan->update(['status' => 'Dibatalkan']);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Maaf! Unit glamping ini baru saja dipesan & dibayar oleh pengunjung lain untuk tanggal yang sama. Pemesanan Anda otomatis dibatalkan.'
                ], 400);
            }
            return redirect()->route('riwayat.index')
                ->with('error', 'Maaf! Unit glamping ini baru saja dipesan & dibayar oleh pengunjung lain untuk tanggal yang sama. Pemesanan Anda otomatis dibatalkan.');
        }

        $paymentInfo = session('payment_info_' . $id_pemesanan, [
            'metode' => 'Midtrans Instant Payment',
            'tipe_bayar' => 'dp',
            'jumlah_bayar' => $pemesanan->total_harga * 0.5,
        ]);

        $metode = $request->input('metode_pembayaran', $paymentInfo['metode']);
        $jumlahBayar = $pemesanan->total_harga * 0.5;

        $metodeText = $metode . ' - DP 50%';

        Pembayaran::create([
            'id_pemesanan' => $pemesanan->id_pemesanan,
            'tanggal_pembayaran' => date('Y-m-d'),
            'jumlah_bayar' => $jumlahBayar,
            'metode_pembayaran' => $metodeText,
            'bukti_pembayaran' => null,
            'status_verifikasi' => 'Diterima',
        ]);

        $pemesanan->update(['status' => 'Dikonfirmasi']);

        // Batalkan pemesanan bentrok HANYA jika stok unit terpakai sudah habis
        $pendingOrders = Pemesanan::where('id_glamping', $pemesanan->id_glamping)
            ->where('id_pemesanan', '!=', $pemesanan->id_pemesanan)
            ->where('status', 'Menunggu Pembayaran')
            ->where('tanggal_checkin', '<', $pemesanan->tanggal_checkout)
            ->where('tanggal_checkout', '>', $pemesanan->tanggal_checkin)
            ->get();

        foreach ($pendingOrders as $po) {
            if ($this->checkAlreadyBookedByOther($po)) {
                $po->update(['status' => 'Dibatalkan']);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('pembayaran.sukses', $pemesanan->id_pemesanan)
            ]);
        }

        return redirect()->route('pembayaran.sukses', $pemesanan->id_pemesanan);
    }

    public function sukses($id_pemesanan)
    {
        $pemesanan = Pemesanan::with(['glamping', 'pembayaran', 'diskon'])
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        return view('user.pembayaran.sukses', compact('pemesanan'));
    }

    public function store(Request $request, $id_pemesanan)
    {
        Pemesanan::cancelExpiredOrders();

        $pemesanan = Pemesanan::where('id_user', Auth::id())->findOrFail($id_pemesanan);

        if ($pemesanan->status == 'Dibatalkan') {
            return redirect()->route('riwayat.index')
                ->with('error', 'Waktu pembayaran 1 jam telah kadaluarsa!');
        }

        if ($this->checkAlreadyBookedByOther($pemesanan)) {
            $pemesanan->update(['status' => 'Dibatalkan']);
            return redirect()->route('riwayat.index')
                ->with('error', 'Maaf! Unit glamping ini baru saja dipesan & dibayar oleh pengunjung lain untuk tanggal yang sama. Pemesanan Anda otomatis dibatalkan.');
        }

        $request->validate([
            'metode_pembayaran' => 'required|string',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $jumlahBayar = $pemesanan->total_harga * 0.5;

        $buktiPath = 'images/bg.jpeg';
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = 'bukti_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/bukti'), $filename);
            $buktiPath = 'images/bukti/' . $filename;
        }

        $metodeKet = $request->metode_pembayaran . ' (DP 50%)';

        Pembayaran::updateOrCreate(
            ['id_pemesanan' => $pemesanan->id_pemesanan],
            [
                'tanggal_pembayaran' => date('Y-m-d'),
                'jumlah_bayar'       => $jumlahBayar,
                'metode_pembayaran'  => $metodeKet,
                'bukti_pembayaran'   => $buktiPath,
                'status_verifikasi'  => 'Menunggu',
            ]
        );

        $pemesanan->update(['status' => 'Menunggu Verifikasi']);

        return redirect()->route('pembayaran.sukses', $pemesanan->id_pemesanan);
    }
}
