<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Form login
    public function login()
    {
        return view('auth.login');
    }

    // Proses login
    public function prosesLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->status_akun == 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda masih menunggu verifikasi dari Admin. Silakan cek email Anda secara berkala.');
            }

            if ($user->status_akun == 'ditolak') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Mohon maaf, pendaftaran akun Anda ditolak oleh Admin.');
            }

            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil login! Selamat datang kembali, Admin.');
            } elseif ($user->role == 'pemilik') {
                return redirect()->route('pemilik.dashboard')->with('success', 'Berhasil login! Selamat datang kembali, ' . $user->nama . '.');
            }

            return redirect()->route('home')->with('success', 'Berhasil login! Selamat datang kembali, ' . $user->nama . '.');
        }

        return back()->with('error', 'Email atau Password salah.');
    }

    // Form registrasi
    public function register()
    {
        return view('auth.register');
    }

    // Proses registrasi
    public function prosesRegister(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|max:20',
            'password' => 'required|min:8|confirmed',
            // 'no_hp' => 'required|numeric|digits_between:10,13',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau langsung masuk (login).',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            // 'no_hp.numeric' => 'Nomor HP harus berupa angka.',
            // 'no_hp.digits_between' => 'Nomor HP harus terdiri dari 10-13 digit.'
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => 'pengunjung',
            'status_akun' => 'disetujui',
        ]);

        // Kirim email notifikasi pendaftaran berhasil
        try {
            sleep(1);
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerifikasiAkunMail($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal kirim email registrasi: ' . $e->getMessage());
        }

        return redirect()->route('login', ['email' => $user->email])
            ->with('success', 'Pendaftaran berhasil! Akun Anda telah aktif. Email notifikasi telah dikirim, silakan login.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar (logout).');
    }
}
