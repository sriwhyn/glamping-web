<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\Glamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminGaleriController extends Controller
{
    public function index()
    {
        $galeris = Galeri::with('glamping')->latest()->get();
        $glampings = Glamping::all();
        return view('admin.galeri.index', compact('galeris', 'glampings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'id_glamping' => 'nullable|exists:glamping,id_glamping',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/galeri'), $filename);
            $fotoPath = 'images/galeri/' . $filename;
        }

        Galeri::create([
            'id_glamping' => $request->id_glamping ?? null,
            'foto' => $fotoPath,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Foto galeri baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $galeri = Galeri::findOrFail($id);

        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'id_glamping' => 'nullable|exists:glamping,id_glamping',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($galeri->foto && File::exists(public_path($galeri->foto))) {
                File::delete(public_path($galeri->foto));
            }

            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/galeri'), $filename);
            $galeri->foto = 'images/galeri/' . $filename;
        }

        $galeri->id_glamping = $request->id_glamping ?? null;
        $galeri->keterangan = $request->keterangan;
        $galeri->save();

        return back()->with('success', 'Data galeri berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $galeri = Galeri::findOrFail($id);

        if ($galeri->foto && File::exists(public_path($galeri->foto))) {
            File::delete(public_path($galeri->foto));
        }

        $galeri->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}
