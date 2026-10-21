<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonFieldsToEventCommittees extends Migration
{
    public function up()
    {
        // Panitia sering berupa daftar nama dari SK/Surat Tugas yang tidak punya akun login.
        // user_id sudah nullable; kolom baru ini menyimpan orangnya langsung.
        Schema::table('event_committees', function (Blueprint $table) {
            $table->string('name', 200)->nullable()->after('user_id');
            $table->string('unit', 150)->nullable()->after('name');      // asal: Rektorat, FEB, FAPERTA, ...
            $table->string('section', 120)->nullable()->after('position'); // seksi: Kesekretariatan, Seksi Acara, ...
            $table->unsignedInteger('sort_order')->default(0)->after('section');
        });
    }

    public function down()
    {
        Schema::table('event_committees', function (Blueprint $table) {
            $table->dropColumn(['name', 'unit', 'section', 'sort_order']);
        });
    }
}
