<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * SoftDeletes + cascade ke data turunan.
 *
 * Foreign key database memakai ON DELETE CASCADE, tapi cascade itu hanya jalan
 * pada hard delete. Pada soft delete, data turunan harus ikut di-soft-delete di
 * level aplikasi, kalau tidak, relasinya jadi menggantung.
 *
 * Deklarasikan di model:
 *   protected array $softCascade = [[Invitation::class, 'participant_id'], ...];
 *
 * Restore hanya memulihkan turunan yang ikut terhapus bersama induknya
 * (deleted_at >= deleted_at induk), bukan yang sudah dihapus lebih dulu.
 */
trait CascadesSoftDeletes
{
    use SoftDeletes;

    public static function bootCascadesSoftDeletes(): void
    {
        static::deleted(function ($model) {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return; // hard delete: biar FK ON DELETE CASCADE yang bekerja
            }

            foreach ($model->softCascade ?? [] as [$child, $fk]) {
                $child::where($fk, $model->getKey())->get()->each->delete();
            }
        });

        static::restored(function ($model) {
            foreach ($model->softCascade ?? [] as [$child, $fk]) {
                $child::onlyTrashed()
                    ->where($fk, $model->getKey())
                    ->where('deleted_at', '>=', $model->getRawOriginal('deleted_at') ?? $model->deleted_at ?? now()->subYears(100))
                    ->get()->each->restore();
            }
        });
    }
}
