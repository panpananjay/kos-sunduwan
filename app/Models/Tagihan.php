<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    // 1. Buka gembok brankas agar bisa diisi nominal dan bukti bayar
    protected $fillable = [
        'penghuni_id', 
        'bulan', 
        'tahun', 
        'jumlah_tagihan', 
        'status', 
        'bukti_bayar',
        'catatan'
    ];

    // 2. Ajari relasi: Tagihan ini tagihannya siapa?
    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    // Ajari relasi: Penghuni ini punya tagihan apa saja?
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }
}