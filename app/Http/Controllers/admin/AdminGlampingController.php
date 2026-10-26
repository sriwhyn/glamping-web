<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Glamping;
use App\Models\Galeri;
use App\Models\TipeGlamping;
use Illuminate\Http\Request;

class AdminGlampingController extends Controller
{
    // Daftar glamping
    public function index()
    {
        $glampings = Glamping::with('tipeGlamping')->latest()->get();
        return view('admin.glamping.index', compact('glampings'));
    }

    // Form tambah glamping
    public function create()
    {
        $tipes = TipeGlamping::all();
        return view('admin.glamping.create', compact('tipes'));
    }

    // Simpan glamping baru
    public function store(Request $request)
    {
        $request->validate([
            'id_tipe' => 'required|exists:tipe_glamping,id_tipe',
            'nama_glamping' => 'required|max:100',
            'harga' => 'required|numeric|min:0',
            'harga_extrabed' => 'nullable|numeric|min:0',
            'kapasitas_dewasa' => 'required|integer|min:1',
            'jumlah_unit' => 'nullable|integer|min:1',
            'deskripsi' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_tambahan.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:tersedia,tidak tersedia',
        ], [
            'gambar.max' => 'Ukuran foto utama maksimal 2MB.',
            'foto_tambahan.*.max' => 'Ukuran setiap foto tambahan maksimal 2MB.',
        ]);

        $gambarPath = 'images/bg.jpeg';
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $gambarPath = 'images/' . $filename;
        }

        $kapasitasDewasa = (int) $request->kapasitas_dewasa;

        $glamping = Glamping::create([
            'id_tipe' => $request->id_tipe,
            'nama_glamping' => $request->nama_glamping,
            'harga' => $request->harga,
            'harga_extrabed' => $request->harga_extrabed,
            'kapasitas' => $kapasitasDewasa + 1,
            'kapasitas_dewasa' => $kapasitasDewasa,
            'jumlah_unit' => $request->input('jumlah_unit', 1),
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath,
            'status' => $request->status,
        ]);

        // Simpan foto galeri
        if ($request->hasFile('foto_tambahan')) {
            foreach ($request->file('foto_tambahan') as $foto) {
                $namaFile = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $foto->move(public_path('images/galeri'), $namaFile);
                Galeri::create([
                    'id_glamping' => $glamping->id_glamping,
                    'foto' => 'images/galeri/' . $namaFile,
                    'keterangan' => $glamping->nama_glamping,
                ]);
            }
        }

        return redirect()->route('admin.glamping.index')->with('success', 'Kategori Glamping berhasil ditambahkan.');
    }

    // Form edit glamping
    public function edit($id)
    {
        $glamping = Glamping::with('galeri')->findOrFail($id);
        $tipes = TipeGlamping::all();
        return view('admin.glamping.edit', compact('glamping', 'tipes'));
    }

    // Update data glamping
    public function update(Request $request, $id)
    {
        $glamping = Glamping::findOrFail($id);

        $request->validate([
            'id_tipe' => 'required|exists:tipe_glamping,id_tipe',
            'nama_glamping' => 'required|max:100',
            'harga' => 'required|numeric|min:0',
            'harga_extrabed' => 'nullable|numeric|min:0',
            'kapasitas_dewasa' => 'required|integer|min:1',
            'jumlah_unit' => 'nullable|integer|min:1',
            'deskripsi' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_tambahan.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:tersedia,tidak tersedia',
        ], [
            'gambar.max' => 'Ukuran foto utama maksimal 2MB.',
            'foto_tambahan.*.max' => 'Ukuran setiap foto tambahan maksimal 2MB.',
        ]);

        $gambarPath = $glamping->gambar;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $gambarPath = 'images/' . $filename;
        }

        $kapasitasDewasa = (int) $request->kapasitas_dewasa;

        $glamping->update([
            'id_tipe' => $request->id_tipe,
            'nama_glamping' => $request->nama_glamping,
            'harga' => $request->harga,
            'harga_extrabed' => $request->harga_extrabed,
            'kapasitas' => $kapasitasDewasa + 1,
            'kapasitas_dewasa' => $kapasitasDewasa,
            'jumlah_unit' => $request->input('jumlah_unit', $glamping->jumlah_unit ?? 1),
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath,
            'status' => $request->status,
        ]);

        // Simpan foto galeri
        if ($request->hasFile('foto_tambahan')) {
            foreach ($request->file('foto_tambahan') as $foto) {
                $namaFile = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $foto->move(public_path('images/galeri'), $namaFile);
                Galeri::create([
                    'id_glamping' => $glamping->id_glamping,
                    'foto' => 'images/galeri/' . $namaFile,
                    'keterangan' => $glamping->nama_glamping,
                ]);
            }
        }

        return redirect()->route('admin.glamping.index')->with('success', 'Kategori Glamping berhasil diperbarui.');
    }

    // Hapus foto galeri
    public function hapusFoto($idGaleri)
    {
        $galeri = Galeri::findOrFail($idGaleri);
        if ($galeri->foto && file_exists(public_path($galeri->foto))) {
            unlink(public_path($galeri->foto));
        }
        $galeri->delete();
        return back()->with('success', 'Foto berhasil dihapus.');
    }

    // Hapus unit glamping
    public function destroy($id)
    {
        $glamping = Glamping::findOrFail($id);

        $adaPemesanan = \App\Models\Pemesanan::where('id_glamping', $id)->exists();

        if ($adaPemesanan) {
            return redirect()->route('admin.glamping.index')
                ->with('error', 'Unit glamping ini tidak dapat dihapus karena memiliki riwayat pemesanan/transaksi. Silakan ubah status ketersediaan menjadi "Tidak Tersedia" jika ingin menonaktifkannya.');
        }

        $glamping->delete();

        return redirect()->route('admin.glamping.index')->with('success', 'Unit Glamping berhasil dihapus.');
    }
}
