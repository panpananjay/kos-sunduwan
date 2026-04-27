<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    // 1. Menampilkan Daftar Pengaduan
    public function index()
    {
        $user = auth()->user();
        
        // Filter buat penghuni
        if ($user->role != 'admin') {
            // LOGIKA HAPUS NOTIF PENGADUAN:
            // Cari pengaduan milik user ini yang statusnya 'diproses' 
            // DAN sudah ada tanggapan dari admin, lalu set jadi 'selesai'
            \App\Models\Pengaduan::where('user_id', $user->id)
                ->where('status', 'diproses')
                ->whereNotNull('tanggapan_admin')
                ->update(['status' => 'selesai']);

            $pengaduans = \App\Models\Pengaduan::where('user_id', $user->id)->latest()->get();
        } else {
            $pengaduans = \App\Models\Pengaduan::latest()->get();
        }

        return view('pengaduan.index', compact('pengaduans'));
    }

    // 2. Menampilkan Form Buat Laporan Baru (Khusus Anak Kos)
    public function create()
    {
        if (Auth::user()->role == 'admin') {
            return redirect()->route('pengaduan.index')->with('error', 'Admin tidak perlu membuat laporan.');
        }
        return view('pengaduan.create');
    }

    // 3. Menyimpan Laporan Baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        $data = [
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status' => 'proses',
        ];

        // Jika ada yang upload foto bukti rusak/bocor
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('pengaduan', 'public');
            $data['foto'] = $fotoPath;
        }

        Pengaduan::create($data);

        return redirect()->route('pengaduan.index')->with('success', 'Laporan berhasil dikirim! Mohon tunggu respon dari Admin.');
    }

    // 4. Admin Memberikan Respon (Update Status & Tanggapan)
    public function respon(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:proses,diproses,selesai',
            'tanggapan_admin' => 'nullable|string',
        ]);

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->update([
            'status' => $request->status,
            'tanggapan_admin' => $request->tanggapan_admin,
        ]);

        return redirect()->route('pengaduan.index')->with('success', 'Respon berhasil dikirim ke penghuni.');
    }

    public function destroy(Pengaduan $pengaduan)
    {
        // Hapus file foto jika ada (Opsional, agar storage tidak penuh)
        if ($pengaduan->foto) {
            \Storage::delete('public/' . $pengaduan->foto);
        }

        $pengaduan->delete();

        return redirect()->back()->with('success', 'Pengaduan berhasil dihapus!');
    }
}