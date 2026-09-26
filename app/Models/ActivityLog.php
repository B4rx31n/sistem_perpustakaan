<?php

namespace App\Models;

use Database\Factories\ActivityLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    /** @use HasFactory<ActivityLogFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_aktor',
        'aksi',
        'subjek',
        'keterangan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Catatan aktivitas untuk jejak audit. Sengaja tidak bisa diubah atau dihapus
     * lewat mass assignment di luar kolom di atas.
     */
    public static function catat(?User $aktor, string $aksi, ?string $subjek = null, ?string $keterangan = null): self
    {
        return static::create([
            'user_id' => $aktor?->id,
            'nama_aktor' => $aktor?->name ?? 'Sistem',
            'aksi' => $aksi,
            'subjek' => $subjek,
            'keterangan' => $keterangan,
        ]);
    }
}
