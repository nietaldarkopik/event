<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacultyContactsToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('wa_pertanian')->nullable()->after('contact_whatsapp');
            $table->string('wa_kehutanan')->nullable()->after('wa_pertanian');
            $table->string('wa_teknik')->nullable()->after('wa_kehutanan');
            $table->string('wa_ekonomi')->nullable()->after('wa_teknik');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['wa_pertanian', 'wa_kehutanan', 'wa_teknik', 'wa_ekonomi']);
        });
    }
}
