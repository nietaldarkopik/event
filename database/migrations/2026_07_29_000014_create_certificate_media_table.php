<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_media', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // logo, signature, stamp, background
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_global')->default(true);
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
        Schema::dropIfExists('certificate_media');
    }
}
