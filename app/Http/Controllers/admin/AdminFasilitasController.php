<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\FasilitasLayanan;
use App\Models\Glamping;
use Illuminate\Http\Request;

class AdminFasilitasController extends Controller
{
    public function index()
    {
        $fasilitas = FasilitasLayanan::with('glamping')->latest()->get();
        $glampings = Glamping::all();
        return view('admin.fasilitas.index', compact('fasilitas', 'glampings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_glamping' => 'required|exists:glamping,id_glamping',
            'nama' => 'required|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/fasilitas'), $filename);
            $gambarPath = 'images/fasilitas/' . $filename;
        }

        FasilitasLayanan::create([
            'id_glamping' => $request->id_glamping,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath,
        ]);

        return back()->with('success', 'Fasilitas layanan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_glamping' => 'required|exists:glamping,id_glamping',
            'nama' => 'required|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $fasilitas = FasilitasLayanan::findOrFail($id);
        $gambarPath = $fasilitas->gambar;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/fasilitas'), $filename);
            $gambarPath = 'images/fasilitas/' . $filename;
        }

        $fasilitas->update([
            'id_glamping' => $request->id_glamping,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath,
        ]);

        return back()->with('success', 'Fasilitas layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $fasilitas = FasilitasLayanan::findOrFail($id);
        $fasilitas->delete();

        return back()->with('success', 'Fasilitas layanan berhasil dihapus.');
    }
}
