<?php

namespace App\Http\Controllers;

use App\Models\FasilitasLayanan;
use App\Models\Galeri;
use App\Models\Glamping;
use App\Models\Diskon;
use App\Models\Pemesanan;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Halaman utama
    public function index(Request $request)
    {
        // Batalkan pesanan kadaluarsa
        Pemesanan::cancelExpiredOrders();

        $checkin = $request->tanggal_checkin;
        $checkout = $request->tanggal_checkout;
        $hasDateFilter = ($checkin && $checkout);

        $glampings = Glamping::with('tipeGlamping')->where('status', 'tersedia')->get();
        $diskons = Diskon::where('status', 'aktif')->get();
        $activeDiskon = $diskons->first();

        $today = date('Y-m-d');

        foreach ($glampings as $g) {
            $totalUnit = (int) ($g->jumlah_unit ?? 1);

            if ($hasDateFilter) {
                // Hitung unit terpesan dengan filter
                $bookedUnits = Pemesanan::where('id_glamping', $g->id_glamping)
                    ->where('status', 'Dikonfirmasi')
                    ->where('tanggal_checkin', '<', $checkout)
                    ->where('tanggal_checkout', '>', $checkin)
                    ->sum('jumlah_unit_pesan');
            } else {
                // Hitung unit terpesan hari ini
                $bookedUnits = Pemesanan::where('id_glamping', $g->id_glamping)
                    ->where('status', 'Dikonfirmasi')
                    ->where('tanggal_checkin', '<=', $today)
                    ->where('tanggal_checkout', '>', $today)
                    ->sum('jumlah_unit_pesan');
            }

            $g->sisa_unit = max(0, $totalUnit - $bookedUnits);
            $g->is_full_today = ($g->sisa_unit <= 0);

            // Cari tanggal tersedia
            if ($g->is_full_today) {
                $startSearch = $hasDateFilter ? $checkout : $today;
                $g->tanggal_tersedia = $this->cariTanggalTersedia($g->id_glamping, $totalUnit, $startSearch);
            } else {
                $g->tanggal_tersedia = null;
            }

            // Hitung diskon (default false jika tidak diset khusus)
            $g->has_diskon = false;
        }

        // Urutkan ketersediaan
        $glampings = $glampings->sortBy('is_full_today')->values();

        $reviews = Review::with(['user', 'glamping'])->latest()->take(3)->get();
        $galeris = Galeri::with('glamping')->latest()->take(6)->get();
        $fasilitas = FasilitasLayanan::with('glamping')->latest()->take(8)->get();

        $promos = $diskons;

        return view('home', compact('glampings', 'diskons', 'promos', 'reviews', 'galeris', 'fasilitas', 'checkin', 'checkout', 'hasDateFilter'));
    }

    // Cari tanggal tersedia
    private function cariTanggalTersedia($idGlamping, $totalUnit, $fromDate)
    {
        for ($i = 1; $i <= 60; $i++) {
            $checkDate = date('Y-m-d', strtotime($fromDate . " +{$i} days"));

            $bookedUnits = Pemesanan::where('id_glamping', $idGlamping)
                ->where('status', 'Dikonfirmasi')
                ->where('tanggal_checkin', '<=', $checkDate)
                ->where('tanggal_checkout', '>', $checkDate)
                ->sum('jumlah_unit_pesan');

            if ($bookedUnits < $totalUnit) {
                return $checkDate;
            }
        }

        return null;
    }

    // Halaman tentang kami
    public function tentang()
    {
        return view('user.tentang');
    }

    // Halaman fasilitas
    public function fasilitas()
    {
        $fasilitas = FasilitasLayanan::with('glamping')->get()->groupBy(function ($item) {
            return $item->glamping->nama_glamping ?? 'Umum';
        });
        return view('user.fasilitas', compact('fasilitas'));
    }

    // Halaman diskon
    public function diskon()
    {
        $diskons = Diskon::where('status', 'aktif')->latest()->get();
        $promos = $diskons;
        return view('user.diskon', compact('diskons', 'promos'));
    }

    public function promo()
    {
        return $this->diskon();
    }

    // Halaman galeri
    public function galeri()
    {
        $galeris = Galeri::with('glamping')->latest()->get();
        return view('user.galeri', compact('galeris'));
    }

    // Halaman kontak
    public function kontak()
    {
        $reviews = Review::with(['user', 'glamping'])->latest()->get();
        return view('user.kontak', compact('reviews'));
    }

    // Kirim pesan WhatsApp
    public function kirimPesan(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'email' => 'required|email|max:100',
            'subjek' => 'required|max:150',
            'pesan' => 'required|string',
        ]);

        $waNumber = '6281234567890';
        $nama = urlencode($request->nama);
        $email = urlencode($request->email);
        $subjek = urlencode($request->subjek);
        $pesan = urlencode($request->pesan);

        $text = "Halo%20Glamping%20Danau%20Kembar%20Scenery,%0A%0ASaya%20ingin%20bertanya%20/%20menyampaikan%20pesan:%0A-%20*Nama*:%20{$nama}%0A-%20*Email*:%20{$email}%0A-%20*Subjek*:%20{$subjek}%0A-%20*Pesan*:%20{$pesan}";

        $waUrl = "https://wa.me/{$waNumber}?text={$text}";

        return redirect()->away($waUrl);
    }
}