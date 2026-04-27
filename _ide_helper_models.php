<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $nomor_kamar
 * @property int $harga
 * @property string $status
 * @property string|null $foto_utama
 * @property string|null $foto_dapur
 * @property string|null $foto_kamar_mandi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Penghuni> $penghunis
 * @property-read int|null $penghunis_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereFotoDapur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereFotoKamarMandi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereFotoUtama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereNomorKamar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereUpdatedAt($value)
 */
	class Kamar extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $judul
 * @property string $deskripsi
 * @property string|null $foto
 * @property string $status
 * @property string|null $tanggapan_admin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan whereTanggapanAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaduan whereUserId($value)
 */
	class Pengaduan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama
 * @property string $no_hp
 * @property int $kamar_id
 * @property int $poin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property-read \App\Models\Kamar $kamar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tagihan> $tagihans
 * @property-read int|null $tagihans_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni whereKamarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni wherePoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghuni whereUserId($value)
 */
	class Penghuni extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $penghuni_id
 * @property string $bulan
 * @property string $tahun
 * @property int $jumlah_tagihan
 * @property string $status
 * @property string|null $bukti_bayar
 * @property string|null $catatan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Penghuni $penghuni
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tagihan> $tagihans
 * @property-read int|null $tagihans_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan whereBuktiBayar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan whereBulan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan whereJumlahTagihan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan wherePenghuniId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan whereTahun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tagihan whereUpdatedAt($value)
 */
	class Tagihan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pengaduan> $pengaduans
 * @property-read int|null $pengaduans_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

