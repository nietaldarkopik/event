<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeatNumberAndRulesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participants', function (Blueprint $table) {
            if (!Schema::hasColumn('participants', 'seat_number')) {
                $table->string('seat_number')->nullable()->after('type'); // Add seat number
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'rules')) {
                $table->longText('rules')->nullable()->after('description'); // Add Tata Tertib
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('seat_number');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('rules');
        });
    }
}
