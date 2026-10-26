<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use Illuminate\Http\Request;

class AdminDiskonController extends Controller
{
    public function index()
    {
        $diskons = Diskon::latest()->get();
        return view('admin.diskon.index', compact('diskons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_diskon' => 'required|max:50|unique:diskon,kode_diskon',
            'nilai_diskon' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Diskon::create([
            'kode_diskon' => strtoupper(trim($request->kode_diskon)),
            'jenis_diskon' => 'nominal',
            'nilai_diskon' => $request->nilai_diskon,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Kode diskon berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_diskon' => 'required|max:50|unique:diskon,kode_diskon,' . $id . ',id_diskon',
            'nilai_diskon' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $diskon = Diskon::findOrFail($id);
        $diskon->update([
            'kode_diskon' => strtoupper(trim($request->kode_diskon)),
            'jenis_diskon' => 'nominal',
            'nilai_diskon' => $request->nilai_diskon,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Kode diskon berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $diskon = Diskon::findOrFail($id);
        $diskon->delete();

        return back()->with('success', 'Kode diskon berhasil dihapus.');
    }
}
