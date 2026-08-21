<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KamarController extends Controller
{
    public function index()
    {
        // Ambil semua data kamar dari database, diurutkan berdasarkan nomor kamar
        $kamars = Kamar::orderBy('nomor_kamar', 'asc')->get();

        // Tampilkan data kamar ke view
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
            'harga' => 'required|numeric',
            'status' => 'required|string',
            'fasilitas' => 'nullable|array',
        ]);

        // Penentuan otomatis tipe kamar berdasarkan fasilitas
        $fasilitasInput = $request->input('fasilitas', []);
        $fasilitasInput = array_filter(array_map('trim', $fasilitasInput));

        $fasilitasTambahan = array_filter($fasilitasInput, function ($item) {
            return $item !== 'Dapur Kecil' && $item !== 'Kamar Mandi Dalam';
        });

        $tipeKamarOtomatis = count($fasilitasTambahan) > 0 ? 'Isian' : 'Kosongan';
        $stringFasilitas = implode(',', $fasilitasInput);

        // Upload foto jika ada
        $foto_utama = $request->hasFile('foto_utama')
            ? $request->file('foto_utama')->store('kamar_fotos', 'public')
            : null;

        $foto_dapur = $request->hasFile('foto_dapur')
            ? $request->file('foto_dapur')->store('kamar_fotos', 'public')
            : null;

        $foto_kamar_mandi = $request->hasFile('foto_kamar_mandi')
            ? $request->file('foto_kamar_mandi')->store('kamar_fotos', 'public')
            : null;

        // Simpan data kamar ke database
        Kamar::create([
            'nomor_kamar' => $request->nomor_kamar,
            'harga' => $request->harga,
            'status' => $request->status,
            'tipe_kamar' => $tipeKamarOtomatis,
            'fasilitas' => $stringFasilitas,
            'foto_utama' => $foto_utama,
            'foto_dapur' => $foto_dapur,
            'foto_kamar_mandi' => $foto_kamar_mandi,
        ]);

        return redirect()
            ->route('kamar.index')
            ->with('success', 'Kamar baru berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        // Cari data kamar berdasarkan ID
        $kamar = Kamar::findOrFail($id);

        // Hapus file foto dari storage
        if ($kamar->foto_utama) {
            Storage::disk('public')->delete($kamar->foto_utama);
        }

        if ($kamar->foto_dapur) {
            Storage::disk('public')->delete($kamar->foto_dapur);
        }

        if ($kamar->foto_kamar_mandi) {
            Storage::disk('public')->delete($kamar->foto_kamar_mandi);
        }

        // Hapus data kamar
        $kamar->delete();

        return redirect()
            ->route('kamar.index')
            ->with('success', 'Data kamar berhasil dihapus.');
    }

    public function edit($id)
    {
        // Cari kamar yang akan diedit
        $kamar = Kamar::findOrFail($id);

        return view('kamar.edit', compact('kamar'));
    }

    public function update(Request $request, $id)
    {
        // Validasi dasar
        $request->validate([
            'nomor_kamar' => 'required|string',
            'harga' => 'required|numeric',
            'status' => 'required|string',
            'fasilitas' => 'nullable|array',
        ]);

        $kamar = Kamar::findOrFail($id);

        // Jika kamar sebelumnya terisi kemudian diubah menjadi kosong,
        // hapus data penghuni yang menempati kamar tersebut
        if ($kamar->status == 'terisi' && $request->status == 'kosong') {
            $kamar->penghunis()->delete();
        }

        // Penentuan otomatis tipe kamar berdasarkan fasilitas
        $fasilitasInput = $request->input('fasilitas', []);
        $fasilitasInput = array_filter(array_map('trim', $fasilitasInput));

        $fasilitasTambahan = array_filter($fasilitasInput, function ($item) {
            return $item !== 'Dapur Kecil' && $item !== 'Kamar Mandi Dalam';
        });

        $tipeKamarOtomatis = count($fasilitasTambahan) > 0 ? 'Isian' : 'Kosongan';
        $stringFasilitas = implode(',', $fasilitasInput);

        // Data utama yang akan diperbarui
        $data = [
            'tipe_kamar' => $tipeKamarOtomatis,
            'nomor_kamar' => $request->nomor_kamar,
            'harga' => $request->harga,
            'status' => $request->status,
            'fasilitas' => $stringFasilitas,
        ];

        // Upload foto utama jika ada foto baru
        if ($request->hasFile('foto_utama')) {
            if ($kamar->foto_utama) {
                Storage::disk('public')->delete($kamar->foto_utama);
            }

            $data['foto_utama'] = $request->file('foto_utama')
                ->store('kamar_fotos', 'public');
        }

        // Upload foto dapur jika ada foto baru
        if ($request->hasFile('foto_dapur')) {
            if ($kamar->foto_dapur) {
                Storage::disk('public')->delete($kamar->foto_dapur);
            }

            $data['foto_dapur'] = $request->file('foto_dapur')
                ->store('kamar_fotos', 'public');
        }

        // Upload foto kamar mandi jika ada foto baru
        if ($request->hasFile('foto_kamar_mandi')) {
            if ($kamar->foto_kamar_mandi) {
                Storage::disk('public')->delete($kamar->foto_kamar_mandi);
            }

            $data['foto_kamar_mandi'] = $request->file('foto_kamar_mandi')
                ->store('kamar_fotos', 'public');
        }

        // Simpan perubahan data kamar
        $kamar->update($data);

        return redirect()
            ->route('kamar.index')
            ->with('success', 'Data kamar berhasil diperbarui.');
    }
}