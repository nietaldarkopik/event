<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Soft delete untuk tabel inti + index unique yang aman untuk data baru.
 *
 * Unique biasa pada kolom seperti invitations.unique_code akan menolak data baru
 * yang kodenya sama dengan baris yang sudah di-soft-delete. Solusinya: index
 * unique dipindah ke kolom virtual yang bernilai NULL untuk baris terhapus
 * (NULL tidak dihitung duplikat), sehingga kode yang sama bisa dipakai lagi.
 */
return new class extends Migration
{
    private array $tables = ['events', 'participants', 'invitations', 'registrations', 'certificates', 'payments'];

    // [tabel, kolom, tipe, nama index lama]
    private array $uniques = [
        ['invitations', 'unique_code', 'VARCHAR(191)', 'invitations_unique_code_unique'],
        ['certificates', 'unique_code', 'VARCHAR(100)', 'certificates_unique_code_unique'],
        ['certificates', 'certificate_hash', 'VARCHAR(255)', 'certificates_certificate_hash_unique'],
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, fn (Blueprint $t) => $t->softDeletes());
            }
        }

        foreach ($this->uniques as [$table, $col, $type, $oldIndex]) {
            $active = "{$col}_active";
            DB::statement("ALTER TABLE `$table` DROP INDEX `$oldIndex`");
            DB::statement("ALTER TABLE `$table` ADD INDEX `{$table}_{$col}_index` (`$col`)");
            DB::statement("ALTER TABLE `$table` ADD COLUMN `$active` $type GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `$col`, NULL)) VIRTUAL");
            DB::statement("ALTER TABLE `$table` ADD UNIQUE INDEX `{$table}_{$active}_unique` (`$active`)");
        }
    }

    public function down(): void
    {
        foreach ($this->uniques as [$table, $col, , $oldIndex]) {
            $active = "{$col}_active";
            DB::statement("ALTER TABLE `$table` DROP INDEX `{$table}_{$active}_unique`");
            DB::statement("ALTER TABLE `$table` DROP COLUMN `$active`");
            DB::statement("ALTER TABLE `$table` DROP INDEX `{$table}_{$col}_index`");
            DB::statement("ALTER TABLE `$table` ADD UNIQUE INDEX `$oldIndex` (`$col`)");
        }

        foreach ($this->tables as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropSoftDeletes());
        }
    }
};
