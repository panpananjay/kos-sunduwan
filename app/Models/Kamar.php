<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $fillable = [
    'nomor_kamar',
    'harga',
    'status',
    'foto_utama',
    'foto_dapur',
    'foto_kamar_mandi',
    ];

    // Ajari relasi: Kamar ini diisi oleh siapa saja?
    public function penghunis()
    {
        return $this->hasMany(Penghuni::class);
    }
}
