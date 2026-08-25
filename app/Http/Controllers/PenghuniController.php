<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class PenghuniController extends Controller
{
    public function index(Request $request)
    {
        // Filter status: default cuma tampilkan yang aktif
        $status = $request->query('status', 'aktif');

        $query = Penghuni::with('kamar');

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        // Bawa datanya ke halaman daftar penghuni
        $penghunis = $query->latest()->get();

        return view('penghuni.index', compact('penghunis', 'status'));
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
            'password.min' => 'Password minimal 8 karakter.',
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
        // Ikut sertakan data user agar variabel $penghuni->username bisa terbaca di blade edit
        $penghuni = \App\Models\Penghuni::with('user')->findOrFail($id);

        // Inject properti username langsung ke objek penghuni dari relasi tabel user (biar clean di blade)
        $penghuni->username = $penghuni->user ? $penghuni->user->username : '';

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

        // 1. Tambahkan validasi username unik, kecuali untuk ID User milik penghuni ini sendiri
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $penghuni->user_id,
            'no_hp' => 'required|string|max:20',
            'kamar_id' => 'required'
        ], [
            'username.unique' => 'Maaf, username ini sudah digunakan oleh akun lain!',
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

        // Simpan perubahan biodatanya ke tabel penghunis
        $penghuni->update([
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'kamar_id' => $request->kamar_id,
        ]);

        // SINKRONISASI BARU: Update Nama DAN Username di akun login (tabel users)
        if ($penghuni->user_id) {
            $user = \App\Models\User::find($penghuni->user_id);
            if ($user) {
                $user->update([
                    'name' => $request->nama,
                    'username' => $request->username // 👤 Username terupdate dengan aman!
                ]);
            }
        }

        return redirect()->route('penghuni.index')->with('success', 'Data penghuni dan akun login berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $penghuni = \App\Models\Penghuni::findOrFail($id);

        // Kosongkan kamarnya (kamar jadi tersedia buat penghuni baru)
        if ($penghuni->kamar_id) {
            $kamar = \App\Models\Kamar::find($penghuni->kamar_id);
            if ($kamar) {
                $kamar->update(['status' => 'kosong']);
            }
        }

        // Nonaktifkan penghuni — BUKAN dihapus, supaya riwayat tagihan tetap tersambung
        $penghuni->update([
            'status' => 'nonaktif',
            'kamar_id' => null,
        ]);

        // Nonaktifkan akun login-nya juga (ganti password acak + jangan hapus,
        // supaya riwayat siapa yang membuat/memverifikasi tagihan tetap utuh)
        if ($penghuni->user_id) {
            $user = \App\Models\User::find($penghuni->user_id);
            if ($user) {
                $user->update([
                    'password' => \Illuminate\Support\Facades\Hash::make(bin2hex(random_bytes(16))),
                ]);
            }
        }

        return redirect()->route('penghuni.index')->with('success', 'Penghuni berhasil dinonaktifkan dan kamarnya otomatis menjadi kosong!');
    }

    // 🆕 MESIN 4: Aktifkan kembali penghuni yang sudah dinonaktifkan
    public function activate($id)
    {
        $penghuni = \App\Models\Penghuni::findOrFail($id);

        $kamarKosong = \App\Models\Kamar::where('status', 'kosong')->first();

        if (!$kamarKosong) {
            return redirect()->back()->with('error', 'Tidak bisa mengaktifkan: tidak ada kamar kosong tersedia. Kosongkan kamar dulu atau edit manual.');
        }

        $penghuni->update([
            'status' => 'aktif',
            'kamar_id' => $kamarKosong->id,
        ]);

        $kamarKosong->update(['status' => 'terisi']);

        return redirect()->route('penghuni.edit', $penghuni->id)->with('success', $penghuni->nama.' berhasil diaktifkan kembali di Kamar '.$kamarKosong->nomor_kamar.'. Silakan sesuaikan datanya kalau perlu.');
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