<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GlampingController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\RiwayatController;

use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminGlampingController;
use App\Http\Controllers\admin\AdminTipeController;
use App\Http\Controllers\admin\AdminPemesananController;
use App\Http\Controllers\admin\AdminPembayaranController;
use App\Http\Controllers\admin\AdminUserController;
use App\Http\Controllers\admin\AdminFasilitasController;
use App\Http\Controllers\admin\AdminDiskonController;

use App\Http\Controllers\ReviewController;
use App\Http\Controllers\admin\AdminReviewController;
use App\Http\Controllers\admin\AdminGaleriController;

use App\Http\Controllers\pemilik\PemilikDashboardController;
use App\Http\Controllers\pemilik\LaporanController;

// Rute publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [HomeController::class, 'tentang'])->name('tentang');
Route::get('/fasilitas', [HomeController::class, 'fasilitas'])->name('fasilitas');
Route::get('/diskon', [HomeController::class, 'diskon'])->name('diskon');
Route::get('/promo', [HomeController::class, 'promo'])->name('promo');
Route::post('/cek-diskon', [ReservasiController::class, 'cekDiskon'])->name('diskon.cek');
Route::get('/galeri', [HomeController::class, 'galeri'])->name('galeri');
Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');
Route::post('/kontak', [HomeController::class, 'kirimPesan'])->name('kontak.kirim');
Route::post('/review', [ReviewController::class, 'store'])->name('review.store');
Route::put('/review/{id}/balas-user', [ReviewController::class, 'balasUser'])->name('review.balasUser');
Route::get('/glamping', [GlampingController::class, 'index'])->name('glamping.index');
Route::get('/glamping/{id}', [GlampingController::class, 'show'])->name('glamping.show');

// Rute tamu
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'prosesLogin'])->name('login.proses');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'prosesRegister'])->name('register.proses');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rute pengunjung
Route::middleware(['auth', 'role:pengunjung'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');

    Route::get('/pemesanan', function () {
        return view('user.pemesanan.index');
    })->name('pemesanan.index');

    Route::post('/reservasi', [ReservasiController::class, 'store'])->name('reservasi.store');
    Route::post('/pemesanan/{id_pemesanan}/reschedule', [ReservasiController::class, 'reschedule'])->name('pemesanan.reschedule');
    Route::delete('/pemesanan/bersihkan-dibatalkan', [ReservasiController::class, 'destroyAllCancelled'])->name('pemesanan.destroy_all_cancelled');
    Route::delete('/pemesanan/{id_pemesanan}', [ReservasiController::class, 'destroy'])->name('pemesanan.destroy');
    Route::get('/pembayaran/{id_pemesanan}', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran/{id_pemesanan}/batalkan', [PembayaranController::class, 'batalkan'])->name('pembayaran.batalkan');
    Route::post('/pembayaran/{id_pemesanan}', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::post('/pembayaran/{id_pemesanan}/proses', [PembayaranController::class, 'prosesPembayaran'])->name('pembayaran.proses');
    Route::post('/pembayaran/{id_pemesanan}/snap-token', [PembayaranController::class, 'getSnapTokenAjax'])->name('pembayaran.snap_token');
    Route::post('/pemesanan/{id_pemesanan}/snap-pelunasan', [PembayaranController::class, 'getSnapTokenPelunasan'])->name('pemesanan.snap_pelunasan');
    Route::post('/pemesanan/{id_pemesanan}/konfirmasi-pelunasan', [PembayaranController::class, 'konfirmasiPelunasanMidtrans'])->name('pemesanan.konfirmasi_pelunasan');
    Route::post('/pemesanan/{id_pemesanan}/snap-denda', [PembayaranController::class, 'getSnapTokenDenda'])->name('pemesanan.snap_denda');
    Route::post('/pemesanan/{id_pemesanan}/konfirmasi-denda', [PembayaranController::class, 'konfirmasiDendaMidtrans'])->name('pemesanan.konfirmasi_denda');
    Route::get('/pembayaran/{id_pemesanan}/instruksi', [PembayaranController::class, 'instruksi'])->name('pembayaran.instruksi');
    Route::post('/pembayaran/{id_pemesanan}/konfirmasi', [PembayaranController::class, 'konfirmasiPay'])->name('pembayaran.konfirmasi');
    Route::get('/pembayaran/{id_pemesanan}/sukses', [PembayaranController::class, 'sukses'])->name('pembayaran.sukses');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

// Rute admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Kelola tipe glamping
    Route::get('/tipe', [AdminTipeController::class, 'index'])->name('tipe.index');
    Route::post('/tipe', [AdminTipeController::class, 'store'])->name('tipe.store');
    Route::put('/tipe/{id}', [AdminTipeController::class, 'update'])->name('tipe.update');
    Route::delete('/tipe/{id}', [AdminTipeController::class, 'destroy'])->name('tipe.destroy');

    // Kelola unit glamping
    Route::resource('glamping', AdminGlampingController::class);
    Route::delete('/glamping/foto/{id}', [AdminGlampingController::class, 'hapusFoto'])->name('glamping.hapusFoto');

    // Kelola Pemesanan
    Route::get('/pemesanan', [AdminPemesananController::class, 'index'])->name('pemesanan.index');
    Route::get('/pemesanan/{id}', [AdminPemesananController::class, 'show'])->name('pemesanan.show');
    Route::put('/pemesanan/{id}/status', [AdminPemesananController::class, 'updateStatus'])->name('pemesanan.status');
    Route::put('/pemesanan/{id}/read-reschedule', [AdminPemesananController::class, 'markRescheduleNotified'])->name('pemesanan.read_reschedule');
    Route::delete('/pemesanan/{id}', [AdminPemesananController::class, 'destroy'])->name('pemesanan.destroy');

    // Verifikasi Pembayaran
    Route::get('/pembayaran', [AdminPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::put('/pembayaran/{id}/verifikasi', [AdminPembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');

    // Kelola pengguna
    Route::get('/user', [AdminUserController::class, 'index'])->name('user.index');
    Route::put('/user/{id}/setujui', [AdminUserController::class, 'setujui'])->name('user.setujui');
    Route::put('/user/{id}/tolak', [AdminUserController::class, 'tolak'])->name('user.tolak');

    // Kelola fasilitas
    Route::get('/fasilitas', [AdminFasilitasController::class, 'index'])->name('fasilitas.index');
    Route::post('/fasilitas', [AdminFasilitasController::class, 'store'])->name('fasilitas.store');
    Route::put('/fasilitas/{id}', [AdminFasilitasController::class, 'update'])->name('fasilitas.update');
    Route::delete('/fasilitas/{id}', [AdminFasilitasController::class, 'destroy'])->name('fasilitas.destroy');

    // Diskon
    Route::get('/diskon', [AdminDiskonController::class, 'index'])->name('diskon.index');
    Route::post('/diskon', [AdminDiskonController::class, 'store'])->name('diskon.store');
    Route::put('/diskon/{id}', [AdminDiskonController::class, 'update'])->name('diskon.update');
    Route::delete('/diskon/{id}', [AdminDiskonController::class, 'destroy'])->name('diskon.destroy');

    // Rute promo
    Route::get('/promo', [AdminDiskonController::class, 'index'])->name('promo.index');
    Route::post('/promo', [AdminDiskonController::class, 'store'])->name('promo.store');
    Route::put('/promo/{id}', [AdminDiskonController::class, 'update'])->name('promo.update');
    Route::delete('/promo/{id}', [AdminDiskonController::class, 'destroy'])->name('promo.destroy');

    // Kelola ulasan
    Route::get('/review', [AdminReviewController::class, 'index'])->name('review.index');
    Route::put('/review/{id}/balas', [AdminReviewController::class, 'balas'])->name('review.balas');
    Route::delete('/review/{id}', [AdminReviewController::class, 'destroy'])->name('review.destroy');

    // Kelola galeri
    Route::get('/galeri', [AdminGaleriController::class, 'index'])->name('galeri.index');
    Route::post('/galeri', [AdminGaleriController::class, 'store'])->name('galeri.store');
    Route::put('/galeri/{id}', [AdminGaleriController::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/{id}', [AdminGaleriController::class, 'destroy'])->name('galeri.destroy');
});

// Rute pemilik & laporan
Route::middleware(['auth', 'role:admin,pemilik'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/dashboard', [PemilikDashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan/pemesanan', [LaporanController::class, 'laporanPemesanan'])->name('laporan.pemesanan');
    Route::get('/laporan/pendapatan', [LaporanController::class, 'laporanPendapatan'])->name('laporan.pendapatan');
    Route::get('/laporan/cetak-pemesanan', [LaporanController::class, 'cetakPemesanan'])->name('laporan.cetak_pemesanan');
    Route::get('/laporan/cetak-pendapatan', [LaporanController::class, 'cetakPendapatan'])->name('laporan.cetak_pendapatan');
});
