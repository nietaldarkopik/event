<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationFieldsToParticipantsTable extends Migration
{
    public function up()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('name')->nullable()->after('event_id');
            $table->string('whatsapp', 30)->nullable()->after('name');
            $table->string('email')->nullable()->after('whatsapp');
            $table->string('nim', 50)->nullable()->after('email');
            $table->string('program_studi')->nullable()->after('nim');
            $table->string('fakultas')->nullable()->after('program_studi');
            $table->string('jabatan')->nullable()->after('fakultas');
            $table->unsignedBigInteger('unit_id')->nullable()->after('unit');
        });
    }

    public function down()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['name', 'whatsapp', 'email', 'nim', 'program_studi', 'fakultas', 'jabatan', 'unit_id']);
        });
    }
}
