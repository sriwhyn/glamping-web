<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'glamping'])->latest()->get();
        return view('admin.review.index', compact('reviews'));
    }

    public function balas(Request $request, $id)
    {
        $request->validate([
            'balasan_admin' => 'required|string',
        ]);

        $review = Review::findOrFail($id);
        $review->update([
            'balasan_admin' => $request->balasan_admin
        ]);

        return back()->with('success', 'Balasan admin berhasil dikirim.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review berhasil dihapus.');
    }
}
