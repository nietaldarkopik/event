<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventAndSessionsTables extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique()->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('title', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('banner', 255)->nullable();
            $table->string('venue_name', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('registration_start')->nullable();
            $table->dateTime('registration_end')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('max_participants')->nullable();
            $table->boolean('certificate_enabled')->default(false);
            $table->boolean('attendance_required')->default(true);
            $table->enum('visibility', ['PUBLIC', 'PRIVATE'])->default('PUBLIC');
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'REGISTRATION_OPEN', 'REGISTRATION_CLOSED', 'FINISHED'])->default('DRAFT');
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('event_categories')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index('start_date', 'idx_events_date');
            $table->index('status', 'idx_events_status');
        });

        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('room', 150)->nullable();
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();
            $table->integer('max_participants')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            $table->index('event_id', 'idx_session_event');
        });

        Schema::create('session_speakers', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('speaker_id');
            $table->primary(['session_id', 'speaker_id']);

            $table->foreign('session_id')->references('id')->on('event_sessions')->onDelete('cascade');
            $table->foreign('speaker_id')->references('id')->on('speakers')->onDelete('cascade');
        });

        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('quota')->nullable();
            $table->dateTime('sale_start')->nullable();
            $table->dateTime('sale_end')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->enum('level', ['GOLD', 'SILVER', 'BRONZE', 'MEDIA'])->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->nullable();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->longText('content')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('event_committees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('position', 100)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_committees');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('ticket_types');
        Schema::dropIfExists('session_speakers');
        Schema::dropIfExists('event_sessions');
        Schema::dropIfExists('events');
    }
}
