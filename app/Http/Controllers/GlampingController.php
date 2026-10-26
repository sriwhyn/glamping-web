<?php

namespace App\Http\Controllers;

use App\Models\Glamping;
use App\Models\TipeGlamping;
use App\Models\Diskon;
use App\Models\Pemesanan;
use App\Models\Review;
use Illuminate\Http\Request;

class GlampingController extends Controller
{

    public function index(Request $request)
    {
        // Batalkan pesanan kadaluarsa
        Pemesanan::cancelExpiredOrders();

        $query = Glamping::with('tipeGlamping')->where('status', 'tersedia');


        // if ($request->filled('cari')) {
        //     $query->where('nama_glamping', 'LIKE', '%' . $request->cari . '%');
        // }

        if ($request->filled('tipe')) {
            $query->where('id_tipe', $request->tipe);
        }

        if ($request->filled('kapasitas')) {
            $query->where('kapasitas', '>=', $request->kapasitas);
        }

        $checkin = $request->tanggal_checkin;
        $checkout = $request->tanggal_checkout;
        $hasDateFilter = ($checkin && $checkout);

        $glampings = $query->latest()->get();
        $diskons = Diskon::where('status', 'aktif')->get();
        $promos = $diskons;

        $today = date('Y-m-d');

        // Hitung sisa unit
        foreach ($glampings as $g) {
            $totalUnit = (int) ($g->jumlah_unit ?? 1);

            if ($hasDateFilter) {
                // Hitung unit terpesan
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
            $g->is_full = ($g->sisa_unit <= 0);

            // Cari tanggal tersedia
            if ($g->is_full) {
                $startSearch = $hasDateFilter ? $checkout : $today;
                $g->tanggal_tersedia = $this->cariTanggalTersedia($g->id_glamping, $totalUnit, $startSearch);
            } else {
                $g->tanggal_tersedia = null;
            }

            // Hitung diskon (default false)
            $g->has_diskon = false;
        }

        // Urutkan ketersediaan
        $glampings = $glampings->sortBy('is_full')->values();

        $tipes = TipeGlamping::all();

        return view('user.glamping.index', compact('glampings', 'tipes', 'diskons', 'promos', 'checkin', 'checkout', 'hasDateFilter'));
    }

    // Detail unit glamping
    public function show($id)
    {
        // Batalkan pesanan kadaluarsa
        Pemesanan::cancelExpiredOrders();

        $glamping = Glamping::with(['tipeGlamping', 'fasilitas', 'galeri'])->findOrFail($id);
        $diskons = Diskon::where('status', 'aktif')->get();
        $promos = $diskons;

        $glamping->has_diskon = false;

        // Ambil booking 2 bulan
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+2 months'));

        $existingBookings = Pemesanan::where('id_glamping', $id)
            ->where('status', 'Dikonfirmasi')
            ->where('tanggal_checkout', '>=', $startDate)
            ->where('tanggal_checkin', '<=', $endDate)
            ->select('tanggal_checkin', 'tanggal_checkout', 'jumlah_unit_pesan')
            ->get();

        // Hitung ketersediaan kalender
        $totalUnit = (int) ($glamping->jumlah_unit ?? 1);
        $calendarData = [];

        for ($i = 0; $i < 60; $i++) {
            $date = date('Y-m-d', strtotime($startDate . " +{$i} days"));
            $bookedUnits = 0;

            foreach ($existingBookings as $b) {
                if ($date >= $b->tanggal_checkin && $date < $b->tanggal_checkout) {
                    $bookedUnits += ($b->jumlah_unit_pesan ?? 1);
                }
            }

            $calendarData[$date] = $bookedUnits;
        }

        // Ambil review unit
        $reviews = Review::with('user')->where('id_glamping', $id)->latest()->get();
        $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : null;

        return view('user.glamping.detail', compact('glamping', 'diskons', 'promos', 'existingBookings', 'calendarData', 'totalUnit', 'reviews', 'avgRating'));
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
}
