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
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('event_slug')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('payload')->nullable(); // JSON payload
            $table->decimal('recaptcha_score', 3, 2)->nullable();
            $table->string('status')->default('pending'); // approved, rejected, spam
            $table->string('reason')->nullable(); // specific reason for rejection
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
