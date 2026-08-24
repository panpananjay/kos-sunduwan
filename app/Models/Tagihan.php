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
        'payment_id',
        'snap_token',
        'payment_type',
        'catatan',
    ];

    /**
     * Relasi ke data Penghuni
     */
    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    /**
     * Relasi ke data Voucher
     */
    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'tagihan_id');
    }
}