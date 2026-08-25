<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'no_hp',
        'kamar_id',
        'poin',
        'status'
    ];

    // Otomatis membatasi poin antara 0 sampai 600
    public function setPoinAttribute($value)
    {
        $this->attributes['poin'] = min(600, max(0, $value));
    }

    /**
     * Relasi Penghuni ke Kamar.
     *
     * penghunis.kamar_id -> kamars.id
     */
    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    /**
     * Relasi Penghuni ke User.
     *
     * penghunis.user_id -> users.id
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi Penghuni ke Tagihan.
     *
     * tagihans.penghuni_id -> penghunis.id
     */
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'penghuni_id');
    }

    /**
     * Relasi Penghuni ke Voucher.
     *
     * vouchers.penghuni_id -> penghunis.id
     */
    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'penghuni_id');
    }
}