<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // WAJIB DITAMBAHKAN untuk mengelola file

class KamarController extends Controller
{
    public function index()
    {
        // 1. ambil semua data kamar dari database
        $kamars = Kamar::all();

        // 2. tampilkan data kamar ke view
        return view('kamar.index', compact('kamars'));
    }

    public function create() 
    {
        return view('kamar.create');
    }

    public function store(Request $request)
    {
        // Proses upload foto jika ada saat membuat kamar baru
        $foto_utama = $request->hasFile('foto_utama') ? $request->file('foto_utama')->store('kamar_fotos', 'public') : null;
        $foto_dapur = $request->hasFile('foto_dapur') ? $request->file('foto_dapur')->store('kamar_fotos', 'public') : null;
        $foto_kamar_mandi = $request->hasFile('foto_kamar_mandi') ? $request->file('foto_kamar_mandi')->store('kamar_fotos', 'public') : null;

        // 1. simpan data kamar ke database beserta path fotonya
        $kamar = Kamar::create([
            'nomor_kamar' => $request->nomor_kamar,
            'harga' => $request->harga,
            'status' => $request->status,
            'foto_utama' => $foto_utama,
            'foto_dapur' => $foto_dapur,
            'foto_kamar_mandi' => $foto_kamar_mandi,
        ]);

        // 2 kembalikan ke halaman data kamar
        return redirect()->route('kamar.index');
    }

    public function destroy($id)
    {
        // 1. cari data kamar berdasarkan id
        $kamar = Kamar::findOrFail($id);

        // Hapus file foto fisik dari storage jika ada, sebelum data di database dihapus
        if ($kamar->foto_utama) { Storage::disk('public')->delete($kamar->foto_utama); }
        if ($kamar->foto_dapur) { Storage::disk('public')->delete($kamar->foto_dapur); }
        if ($kamar->foto_kamar_mandi) { Storage::disk('public')->delete($kamar->foto_kamar_mandi); }

        // 2. hapus data kamar
        $kamar->delete();

        // 3. kembalikan ke halaman daftar kamar
        return redirect()->route('kamar.index');
    }

    public function edit($id)
    {
        // Cari kamar yang mau diedit, lalu bawa datanya ke form edit
        $kamar = Kamar::findOrFail($id);
        return view('kamar.edit', compact('kamar'));
    }

    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        // Jika status awal kamar 'terisi', tapi admin mengubahnya jadi 'kosong'
        if ($kamar->status == 'terisi' && $request->status == 'kosong') {
            // Otomatis hapus data anak kos yang menempati kamar tersebut!
            $kamar->penghunis()->delete();
        }

        // Siapkan array data yang akan diupdate
        $data = [
            'nomor_kamar' => $request->nomor_kamar,
            'harga' => $request->harga,
            'status' => $request->status,
        ];

        // LOGIKA UPLOAD FOTO SAAT EDIT
        // Jika ada file foto utama yang diupload admin
        if ($request->hasFile('foto_utama')) {
            // Hapus foto lama agar tidak menumpuk di server
            if ($kamar->foto_utama) { Storage::disk('public')->delete($kamar->foto_utama); }
            // Simpan foto baru dan masukkan path-nya ke array data
            $data['foto_utama'] = $request->file('foto_utama')->store('kamar_fotos', 'public');
        }

        // Jika ada file foto dapur yang diupload admin
        if ($request->hasFile('foto_dapur')) {
            if ($kamar->foto_dapur) { Storage::disk('public')->delete($kamar->foto_dapur); }
            $data['foto_dapur'] = $request->file('foto_dapur')->store('kamar_fotos', 'public');
        }

        // Jika ada file foto kamar mandi yang diupload admin
        if ($request->hasFile('foto_kamar_mandi')) {
            if ($kamar->foto_kamar_mandi) { Storage::disk('public')->delete($kamar->foto_kamar_mandi); }
            $data['foto_kamar_mandi'] = $request->file('foto_kamar_mandi')->store('kamar_fotos', 'public');
        }

        // Simpan perubahan data kamarnya ke database
        $kamar->update($data);

        // Kembalikan ke halaman daftar kamar
        return redirect()->route('kamar.index');
    }
}