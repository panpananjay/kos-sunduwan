<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory;

    // Perbaikan: Menghapus duplikasi tagihan_id
    protected $fillable = [
        'penghuni_id',
        'tagihan_id',
        'kode_voucher',
        'nominal',
        'status',
        'masa_berlaku',
    ];

    // Relasi: Voucher ini milik seorang penghuni
    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class, 'penghuni_id');
    }

    // Relasi: Voucher ini dipakai di tagihan mana
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    /**
     * Accessor untuk mengecek status secara dinamis.
     * Jadi status 'expired' bisa langsung dibaca di Blade/Frontend 
     * tanpa perlu sering-sering query UPDATE ke database.
     */
    public function getStatusAttribute($value)
    {
        if ($value === 'aktif' && Carbon::now()->gt($this->masa_berlaku)) {
            return 'expired';
        }
        return $value;
    }
}