<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionSupportToParticipantsAndEvents extends Migration
{
    public function up()
    {
        // Sesi peserta (mis. Wisuda sesi 1 / sesi 2). Nullable: event tanpa sesi tetap berjalan seperti biasa.
        Schema::table('participants', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable()->after('event_id')->index();
        });

        // Cara sesi ditentukan: none = tanpa sesi, admin = ditetapkan panitia, choose = dipilih peserta (dibatasi kuota sesi bila ada).
        Schema::table('events', function (Blueprint $table) {
            $table->string('session_mode', 20)->default('none');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('session_mode');
        });
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
    }
}
