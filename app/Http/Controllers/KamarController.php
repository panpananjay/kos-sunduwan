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
        // Validasi dasar saat membuat kamar baru
        $request->validate([
            'nomor_kamar' => 'required|string',
            'harga'       => 'required|numeric',
            'status'      => 'required|string',
            'fasilitas'   => 'nullable|array',
        ]);

        // 1. Logika Penentuan Otomatis Tipe Kamar (Store)
        $fasilitasInput = $request->input('fasilitas', []);
        // Bersihkan spasi kosong di setiap elemen array
        $fasilitasInput = array_filter(array_map('trim', $fasilitasInput));

        // Filter: Ambil fasilitas selain Dapur Kecil dan Kamar Mandi Dalam
        $fasilitasTambahan = array_filter($fasilitasInput, function($item) {
            return $item !== 'Dapur Kecil' && $item !== 'Kamar Mandi Dalam';
        });

        // Jika ada fasilitas selain 2 item di atas, maka Isian. Jika tidak ada, maka Kosongan.
        $tipeKamarOtomatis = count($fasilitasTambahan) > 0 ? 'Isian' : 'Kosongan';

        $stringFasilitas = implode(',', $fasilitasInput);

        // Proses upload foto jika ada saat membuat kamar baru
        $foto_utama = $request->hasFile('foto_utama') ? $request->file('foto_utama')->store('kamar_fotos', 'public') : null;
        $foto_dapur = $request->hasFile('foto_dapur') ? $request->file('foto_dapur')->store('kamar_fotos', 'public') : null;
        $foto_kamar_mandi = $request->hasFile('foto_kamar_mandi') ? $request->file('foto_kamar_mandi')->store('kamar_fotos', 'public') : null;

        // 2. simpan data kamar ke database beserta path fotonya
        $kamar = Kamar::create([
            'nomor_kamar' => $request->nomor_kamar,
            'harga'       => $request->harga,
            'status'      => $request->status,
            'tipe_kamar'  => $tipeKamarOtomatis, // Otomatis tersimpan dari hasil kalkulasi filter
            'fasilitas'   => $stringFasilitas,
            'foto_utama'  => $foto_utama,
            'foto_dapur'  => $foto_dapur,
            'foto_kamar_mandi' => $foto_kamar_mandi,
        ]);

        // 3. kembalikan ke halaman data kamar
        return redirect()->route('kamar.index')->with('success', 'Kamar baru berhasil ditambahkan.');
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
        return redirect()->route('kamar.index')->with('success', 'Data kamar berhasil dihapus.');
    }

    public function edit($id)
    {
        // Cari kamar yang mau diedit, lalu bawa datanya ke form edit
        $kamar = Kamar::findOrFail($id);
        return view('kamar.edit', compact('kamar'));
    }

    public function update(Request $request, $id)
    {
        // 1. Jalankan validasi dasar (tipe_kamar dihapus dari validation rule karena diatur otomatis)
        $request->validate([
            'nomor_kamar' => 'required|string',
            'harga'       => 'required|numeric',
            'status'      => 'required|string',
            'fasilitas'   => 'nullable|array',
        ]);

        $kamar = Kamar::findOrFail($id);

        // 2. Logika aman: Jika status awal kamar 'terisi', tapi admin mengubahnya jadi 'kosong'
        if ($kamar->status == 'terisi' && $request->status == 'kosong') {
            // Otomatis hapus data anak kos yang menempati kamar tersebut!
            $kamar->penghunis()->delete();
        }

        // 3. Logika Penentuan Otomatis Tipe Kamar (Update)
        $fasilitasInput = $request->input('fasilitas', []);
        $fasilitasInput = array_filter(array_map('trim', $fasilitasInput));

        // Filter: Cek apakah ada fasilitas selain Dapur Kecil & Kamar Mandi Dalam
        $fasilitasTambahan = array_filter($fasilitasInput, function($item) {
            return $item !== 'Dapur Kecil' && $item !== 'Kamar Mandi Dalam';
        });

        // Set tipe kamar berdasarkan hasil filter array fasilitas
        $tipeKamarOtomatis = count($fasilitasTambahan) > 0 ? 'Isian' : 'Kosongan';

        $stringFasilitas = implode(',', $fasilitasInput);

        // 4. Siapkan array data utama yang akan diupdate ke database
        $data = [
            'tipe_kamar'  => $tipeKamarOtomatis, // Otomatis memperbarui berdasarkan kondisi checkbox terbaru
            'nomor_kamar' => $request->nomor_kamar,
            'harga'       => $request->harga,
            'status'      => $request->status,
            'fasilitas'   => $stringFasilitas, 
        ];

        // 5. LOGIKA UPLOAD FOTO SAAT EDIT (Tetap aman & hapus foto lama jika ditimpa)
        if ($request->hasFile('foto_utama')) {
            if ($kamar->foto_utama) { Storage::disk('public')->delete($kamar->foto_utama); }
            $data['foto_utama'] = $request->file('foto_utama')->store('kamar_fotos', 'public');
        }

        if ($request->hasFile('foto_dapur')) {
            if ($kamar->foto_dapur) { Storage::disk('public')->delete($kamar->foto_dapur); }
            $data['foto_dapur'] = $request->file('foto_dapur')->store('kamar_fotos', 'public');
        }

        if ($request->hasFile('foto_kamar_mandi')) {
            if ($kamar->foto_kamar_mandi) { Storage::disk('public')->delete($kamar->foto_kamar_mandi); }
            $data['foto_kamar_mandi'] = $request->file('foto_kamar_mandi')->store('kamar_fotos', 'public');
        }

        // 6. Simpan perubahan seluruh data kamarnya ke database
        $kamar->update($data);

        // 7. Kembalikan ke halaman daftar kamar dengan flash message alert sukses
        return redirect()->route('kamar.index')->with('success', 'Data kamar berhasil diperbarui.');
    }
}