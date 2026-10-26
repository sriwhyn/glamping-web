<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TipeGlamping;
use Illuminate\Http\Request;

class AdminTipeController extends Controller
{
    public function index()
    {
        $tipes = TipeGlamping::all();
        return view('admin.tipe.index', compact('tipes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tipe' => 'required|max:100|unique:tipe_glamping,nama_tipe',
        ]);

        TipeGlamping::create([
            'nama_tipe' => $request->nama_tipe,
        ]);

        return back()->with('success', 'Tipe glamping berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $tipe = TipeGlamping::findOrFail($id);

        $request->validate([
            'nama_tipe' => 'required|max:100|unique:tipe_glamping,nama_tipe,' . $id . ',id_tipe',
        ]);

        $tipe->update([
            'nama_tipe' => $request->nama_tipe,
        ]);

        return back()->with('success', 'Tipe glamping berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tipe = TipeGlamping::findOrFail($id);

        $adaGlamping = \App\Models\Glamping::where('id_tipe', $id)->exists();

        if ($adaGlamping) {
            return back()->with('error', 'Tipe glamping ini tidak dapat dihapus karena masih digunakan oleh beberapa unit glamping.');
        }

        $tipe->delete();

        return back()->with('success', 'Tipe glamping berhasil dihapus.');
    }
}
