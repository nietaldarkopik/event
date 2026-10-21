<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('type')->nullable(); // internal, external, student, faculty, staff, etc.
            $table->string('event_id')->nullable();
            $table->string('unit')->nullable(); // unit name
            $table->string('status')->default('approved'); // approved, pending_payment, rejected
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
            $table->dropColumn(['status']);
            $table->dropColumn(['unit']);
            $table->dropColumn(['event_id']);
            $table->dropColumn(['type']);
        });
    }
};
