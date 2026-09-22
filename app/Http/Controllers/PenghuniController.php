<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PenghuniController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'aktif');

        $query = Penghuni::with('kamar');

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        $penghunis = $query->latest()->get();

        return view('penghuni.index', compact('penghunis', 'status'));
    }

    public function create()
    {
        $kamarKosong = Kamar::where('status', 'kosong')->get();

        return view('penghuni.create', compact('kamarKosong'));
    }

    public function store(Request $request)
    {
        // 1. SATPAM FORMULIR: Cek ketat sebelum data masuk brankas!
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp' => ['required', 'string', 'regex:/^08[0-9]{8,11}$/'],
            'kamar_id' => 'required',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8',
        ], [
            'nama.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'no_hp.regex' => 'Nomor HP/WhatsApp harus diawali 08 dan berupa nomor Indonesia yang valid.',
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
        $penghuniBaru = \App\Models\Penghuni::create([
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

        // 👇 KABEL PENGHUBUNG 2: TERBITKAN TAGIHAN PERTAMA OTOMATIS 👇
        Carbon::setLocale('id');
        $bulanIni = Carbon::now()->translatedFormat('F');
        $tahunIni = Carbon::now()->year;
        $penghuniBaru->load('kamar');
        app(\App\Http\Controllers\TagihanController::class)
            ->terbitkanTagihanUntukPenghuni($penghuniBaru, $bulanIni, $tahunIni);

        return redirect()->route('penghuni.index')->with('success', 'Penghuni baru dan akun login berhasil ditambahkan, Kamar otomatis terisi, tagihan pertama otomatis terbit!');
    }

    // MESIN 1: Untuk menampilkan formulir Edit
    public function edit($id)
    {
        $penghuni = \App\Models\Penghuni::with('user')->findOrFail($id);

        $penghuni->username = $penghuni->user ? $penghuni->user->username : '';

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
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'username' => 'required|string|max:255|unique:users,username,' . $penghuni->user_id,
            'no_hp' => ['required', 'string', 'regex:/^08[0-9]{8,11}$/'],
            'kamar_id' => 'required'
        ], [
            'nama.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'no_hp.regex' => 'Nomor HP/WhatsApp harus diawali 08 dan berupa nomor Indonesia yang valid.',
            'username.unique' => 'Maaf, username ini sudah digunakan oleh akun lain!',
        ]);

        if ($penghuni->kamar_id != $request->kamar_id) {
            $kamarLama = \App\Models\Kamar::find($penghuni->kamar_id);
            if ($kamarLama) {
                $kamarLama->update(['status' => 'kosong']);
            }

            $kamarBaru = \App\Models\Kamar::find($request->kamar_id);
            if ($kamarBaru) {
                $kamarBaru->update(['status' => 'terisi']);
            }
        }

        $penghuni->update([
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'kamar_id' => $request->kamar_id,
        ]);

        if ($penghuni->user_id) {
            $user = \App\Models\User::find($penghuni->user_id);
            if ($user) {
                $user->update([
                    'name' => $request->nama,
                    'username' => $request->username
                ]);
            }
        }

        return redirect()->route('penghuni.index')->with('success', 'Data penghuni dan akun login berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $penghuni = \App\Models\Penghuni::findOrFail($id);

        if ($penghuni->kamar_id) {
            $kamar = \App\Models\Kamar::find($penghuni->kamar_id);
            if ($kamar) {
                $kamar->update(['status' => 'kosong']);
            }
        }

        $penghuni->update([
            'status' => 'nonaktif',
            'kamar_id' => null,
        ]);

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

    // MESIN 4: Aktifkan kembali penghuni yang sudah dinonaktifkan
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

        // 👇 TERBITKAN TAGIHAN PERTAMA OTOMATIS, SAMA SEPERTI PENDAFTARAN BARU 👇
        Carbon::setLocale('id');
        $bulanIni = Carbon::now()->translatedFormat('F');
        $tahunIni = Carbon::now()->year;
        $penghuni->load('kamar');
        app(\App\Http\Controllers\TagihanController::class)
            ->terbitkanTagihanUntukPenghuni($penghuni, $bulanIni, $tahunIni);

        return redirect()->route('penghuni.edit', $penghuni->id)->with('success', $penghuni->nama.' berhasil diaktifkan kembali di Kamar '.$kamarKosong->nomor_kamar.', tagihan periode ini otomatis terbit. Silakan sesuaikan datanya kalau perlu.');
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