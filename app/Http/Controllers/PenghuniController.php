<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class PenghuniController extends Controller
{
    public function index()
    {
        // Ambil semua data penghuni, dan sekalian 'gandeng' data kamarnya (Eager Loading)
        $penghunis = Penghuni::with('kamar')->get();
        
        // Bawa datanya ke halaman daftar penghuni
        return view('penghuni.index', compact('penghunis'));
    }

    public function create()
    {
        // Cari HANYA kamar yang statusnya kosong
        $kamarKosong = Kamar::where('status', 'kosong')->get();

        // Bawa data kamar kosong itu ke halaman formulir penghuni
        return view('penghuni.create', compact('kamarKosong'));
    }

    public function store(Request $request)
    {
        // 1. SATPAM FORMULIR: Cek ketat sebelum data masuk brankas!
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'kamar_id' => 'required',
            'username' => 'required|string|unique:users,username', // 👈 Kunci Anti-Duplikat
            'password' => 'required|string|min:8',
        ], [
            'username.unique' => 'Maaf, username ini sudah dipakai! Silakan pilih nama lain.',
            'password.min' => 'Password minimal harus 8 karakter ya.',
        ]);

        // 2. Buat Akun Login Dulu (Tabel Users)
        $userBaru = \App\Models\User::create([
            'name' => $request->nama,
            'username' => $request->username,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'penghuni',
        ]);

        // 3. Simpan Biodata Penghuni (Tabel Penghunis)
        \App\Models\Penghuni::create([
            'user_id' => $userBaru->id,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'kamar_id' => $request->kamar_id,
        ]);

        // 👇 KABEL PENGHUBUNG 1: OTOMATIS UBAH STATUS KAMAR JADI 'TERISI' 👇
        $kamarDipilih = \App\Models\Kamar::find($request->kamar_id);
        if ($kamarDipilih) {
            $kamarDipilih->update(['status' => 'terisi']);
        }

        return redirect()->route('penghuni.index')->with('success', 'Penghuni baru dan akun login berhasil ditambahkan, Kamar otomatis terisi!');
    }

    // MESIN 1: Untuk menampilkan formulir Edit
    public function edit($id)
    {
        $penghuni = \App\Models\Penghuni::findOrFail($id);
        
        // Ambil daftar kamar (Hanya kamar kosong + kamar yang sedang dipakai anak ini)
        $kamars = \App\Models\Kamar::where('status', 'kosong')
                                  ->orWhere('id', $penghuni->kamar_id)
                                  ->get();

        return view('penghuni.edit', compact('penghuni', 'kamars'));
    }

    // MESIN 2: Untuk memproses data setelah tombol "Simpan" diklik
    public function update(Request $request, $id)
    {
        $penghuni = \App\Models\Penghuni::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'kamar_id' => 'required'
        ]);

        // KABEL PENGHUBUNG 2: Cek apakah penghuni ini PINDAH KAMAR?
        if ($penghuni->kamar_id != $request->kamar_id) {
            // Kalau pindah: Kamar lama diubah jadi "kosong"
            $kamarLama = \App\Models\Kamar::find($penghuni->kamar_id);
            if ($kamarLama) {
                $kamarLama->update(['status' => 'kosong']);
            }

            // Kamar baru diubah jadi "terisi"
            $kamarBaru = \App\Models\Kamar::find($request->kamar_id);
            if ($kamarBaru) {
                $kamarBaru->update(['status' => 'terisi']);
            }
        }

        // Simpan perubahan biodatanya
        $penghuni->update([
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'kamar_id' => $request->kamar_id,
        ]);

        // Update nama di akun loginnya juga
        if ($penghuni->user_id) {
            $user = \App\Models\User::find($penghuni->user_id);
            if ($user) {
                $user->update(['name' => $request->nama]);
            }
        }

        return redirect()->route('penghuni.index')->with('success', 'Data penghuni berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $penghuni = \App\Models\Penghuni::findOrFail($id);
        
        // 👇 KABEL PENGHUBUNG 3: KOSONGKAN KAMARNYA SEBELUM DIHAPUS 👇
        if ($penghuni->kamar_id) {
            $kamar = \App\Models\Kamar::find($penghuni->kamar_id);
            if ($kamar) {
                $kamar->update(['status' => 'kosong']);
            }
        }

        // Catat ID Akun Login-nya sebelum biodatanya dibakar
        $idAkun = $penghuni->user_id;

        // Bakar biodata penghuni dari tabel penghunis
        $penghuni->delete();

        // HANCURKAN JUGA AKUN LOGIN-NYA!
        if ($idAkun) {
            \App\Models\User::destroy($idAkun);
        }

        return redirect()->route('penghuni.index')->with('success', 'Data penghuni dihapus dan kamarnya otomatis menjadi kosong!');
    }

    // MESIN 3: Untuk mereset password anak kos yang pelupa
    public function resetPassword($id)
    {
        $penghuni = \App\Models\Penghuni::findOrFail($id);

        if ($penghuni->user_id) {
            $user = \App\Models\User::find($penghuni->user_id);
            if ($user) {
                $user->update([
                    'password' => \Illuminate\Support\Facades\Hash::make('12345678')
                ]);
                return redirect()->back()->with('success', 'Berhasil! Password '.$penghuni->nama.' telah direset menjadi: 12345678');
            }
        }

        return redirect()->back()->with('error', 'Gagal mereset. Penghuni ini belum memiliki akun login.');
    }
}