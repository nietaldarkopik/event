<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceExistingCertificateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Enhance Certificate Templates Table
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->json('layout_json')->nullable()->after('text_color');
            $table->boolean('is_locked')->default(false)->after('layout_json');
            $table->boolean('is_default')->default(false)->after('is_locked');
            $table->foreignId('created_by')->nullable()->after('is_default')->constrained('users')->onDelete('set null');
        });

        // 2. Enhance Certificate Signatures Table
        Schema::table('certificate_signatures', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('template_id')->constrained('events')->onDelete('cascade');
            $table->boolean('show_name')->default(true)->after('signer_name');
            $table->boolean('show_title')->default(true)->after('signer_position');
            $table->string('font_family')->default('Times New Roman')->after('is_active');
            $table->integer('font_size')->default(12)->after('font_family');
            $table->string('text_alignment')->default('center')->after('font_size');
            $table->string('stamp_image_path')->nullable()->after('text_alignment');
            $table->integer('stamp_opacity')->default(40)->after('stamp_image_path');
            $table->integer('stamp_rotation')->default(0)->after('stamp_opacity');
            $table->string('stamp_layer')->default('below')->after('stamp_rotation');
        });
    }

    public function down()
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['layout_json', 'is_locked', 'is_default', 'created_by']);
        });

        Schema::table('certificate_signatures', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn([
                'event_id',
                'show_name',
                'show_title',
                'font_family',
                'font_size',
                'text_alignment',
                'stamp_image_path',
                'stamp_opacity',
                'stamp_rotation',
                'stamp_layer'
            ]);
        });
    }
}
