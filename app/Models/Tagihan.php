<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'penghuni_id',
        'bulan',
        'tahun',
        'jumlah_tagihan',
        'status',
        'bukti_bayar',
        'catatan'
    ];

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'tagihan_id');
    }
}