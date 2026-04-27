<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    use HasFactory;

    // 1. Buka gembok brankas (Sudah aman dari jebakan Batman!)
    protected $fillable = [
        'user_id', 
        'nama', 
        'no_hp', 
        'kamar_id', 
        'poin',
    ];

    // 2. Ajari relasi: Penghuni ini menyewa di Kamar mana?
    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    // 3. Ajari relasi: Penghuni ini pakai akun login yang mana?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 4. INI YANG TADI SEMPAT HILANG: Penghuni ini punya tagihan apa saja?
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }
}