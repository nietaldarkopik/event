<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCommitteeRecipientToCertificates extends Migration
{
    public function up()
    {
        // Penerima sertifikat bisa peserta ATAU panitia (event_committees). Nama & asal disalin saat terbit
        // sehingga sertifikat tetap benar walau data panitia/peserta diubah atau dihapus kemudian.
        Schema::table('certificates', function (Blueprint $table) {
            $table->unsignedBigInteger('committee_id')->nullable()->after('participant_id')->index();
            $table->string('recipient_name', 200)->nullable()->after('committee_id');
            $table->string('recipient_unit', 200)->nullable()->after('recipient_name');
        });

        // Log audit untuk sertifikat panitia tidak punya participant_id.
        DB::statement('ALTER TABLE certificate_audit_logs MODIFY participant_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['committee_id', 'recipient_name', 'recipient_unit']);
        });
        DB::statement('ALTER TABLE certificate_audit_logs MODIFY participant_id BIGINT UNSIGNED NOT NULL');
    }
}
