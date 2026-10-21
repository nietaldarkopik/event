<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageUrlToWaLogsTable extends Migration
{
    public function up()
    {
        // Create wa_logs table if not exists
        if (!Schema::hasTable('wa_logs')) {
            Schema::create('wa_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('participant_id')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('message_type', 50)->nullable();
                $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
                $table->text('response_message')->nullable();
                $table->string('image_url', 500)->nullable();
                $table->dateTime('sent_at')->nullable();
                $table->timestamps();

                $table->index('participant_id');
                $table->index('status');
            });
        } else {
            // If table exists, just add image_url column
            Schema::table('wa_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('wa_logs', 'image_url')) {
                    $table->string('image_url', 500)->nullable()->after('response_message');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('wa_logs');
    }
}
