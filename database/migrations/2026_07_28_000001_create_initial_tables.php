<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInitialTables extends Migration
{
    public function up()
    {


        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('name', 150);
            $table->string('email', 150)->unique()->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('google_id', 255)->nullable();
            $table->string('unwim_id', 255)->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique()->nullable();
            $table->string('name', 200)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('website', 200)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('slug')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('icon', 100)->nullable();
            $table->string('color', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('speakers', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->nullable();
            $table->string('name', 150)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('title', 150)->nullable();
            $table->string('photo', 255)->nullable();
            $table->longText('bio')->nullable();
            $table->string('linkedin', 255)->nullable();
            $table->string('instagram', 255)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('speakers');
        Schema::dropIfExists('event_categories');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('users');
    }
}
