<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerifikasiAkunMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    // Tampilkan daftar pengguna
    public function index()
    {
        $users = User::whereIn('role', ['pengunjung', 'pemilik'])->latest()->get();
        return view('admin.user.index', compact('users'));
    }

    // Setujui pendaftaran pengguna & kirim email notifikasi
    public function setujui($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status_akun' => 'disetujui']);

        $pesanEmail = '';
        try {
            // Beri jeda 1 detik untuk menghindari rate limit Mailtrap Free (maks 1 email/detik)
            sleep(1);
            Mail::to($user->email)->send(new VerifikasiAkunMail($user));
            $pesanEmail = ' Email notifikasi verifikasi telah dikirim ke ' . $user->email . ' (Mailtrap).';
        } catch (\Exception $e) {
            // Jika terkena batasan rate limit Mailtrap (Too many emails per second), coba kirim ulang setelah 2 detik
            try {
                sleep(2);
                Mail::to($user->email)->send(new VerifikasiAkunMail($user));
                $pesanEmail = ' Email notifikasi verifikasi telah dikirim ke ' . $user->email . ' (Mailtrap).';
            } catch (\Exception $ex) {
                Log::error('Gagal mengirim email verifikasi: ' . $ex->getMessage());
                $pesanEmail = ' (Catatan: Gagal mengontak server email: ' . $ex->getMessage() . ')';
            }
        }

        return back()->with('success', 'Akun ' . $user->nama . ' berhasil disetujui.' . $pesanEmail);
    }

    // Tolak pendaftaran pengguna
    public function tolak($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status_akun' => 'ditolak']);

        return back()->with('success', 'Pendaftaran akun ' . $user->nama . ' telah ditolak.');
    }
}
