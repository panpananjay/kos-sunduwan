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
    ];

    // 🆕 MUTATOR BARU: Otomatis membatasi poin maksimal 600 sebelum disimpan ke database
    public function setPoinAttribute($value)
    {
        // min(600, $value) artinya jika nilainya diatas 600, yang diambil tetep 600
        $this->attributes['poin'] = min(600, max(0, $value));
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'penghuni_id');
    }
}