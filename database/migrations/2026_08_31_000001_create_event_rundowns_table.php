<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRundownsTable extends Migration
{
    public function up()
    {
        Schema::create('event_rundowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->time('time_start');
            $table->time('time_end');
            $table->string('agenda');
            $table->integer('order_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_rundowns');
    }
}
