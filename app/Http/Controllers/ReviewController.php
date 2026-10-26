<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Simpan atau perbarui ulasan
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk memberikan ulasan.');
        }

        $request->validate([
            'rating'       => 'required|integer|min:1|max:5',
            'komentar'     => 'required|string',
            'id_glamping'  => 'nullable|exists:glamping,id_glamping',
            'id_pemesanan' => 'nullable|exists:pemesanan,id_pemesanan',
        ], [
            'rating.required'   => 'Pilih rating bintang (1-5).',
            'komentar.required' => 'Tuliskan ulasan atau ulasan singkat Anda.',
        ]);

        // Cek ulasan lama
        if ($request->filled('id_pemesanan')) {
            $existing = Review::where('id_pemesanan', $request->id_pemesanan)
                ->where('id_user', Auth::id())
                ->first();

            if ($existing) {
                $existing->update([
                    'rating'   => $request->rating,
                    'komentar' => $request->komentar,
                    'subjek'   => $request->subjek ?? 'Ulasan Pengunjung',
                ]);

                return back()->with('success', 'Ulasan & rating Anda berhasil diperbarui!');
            }
        }

        Review::create([
            'id_user'      => Auth::id(),
            'id_glamping'  => $request->id_glamping ?? null,
            'id_pemesanan' => $request->id_pemesanan ?? null,
            'subjek'       => $request->subjek ?? 'Ulasan Pengunjung',
            'rating'       => $request->rating,
            'komentar'     => $request->komentar,
        ]);

        return back()->with('success', 'Ulasan & rating Anda berhasil dikirim! Terima kasih atas pengalaman yang Anda bagikan.');
    }

    // Tanggapan ulasan user
    public function balasUser(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'balasan_user' => 'required|string',
        ]);

        $review = Review::where('id_review', $id)->where('id_user', Auth::id())->firstOrFail();
        $review->update([
            'balasan_user' => $request->balasan_user,
        ]);

        return back()->with('success', 'Tanggapan Anda telah terkirim!');
    }
}
