<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Several app/Models/* classes (AttendanceLog, Payment, EventCategory, Certificate,
 * User) were written for columns that never made it into the migrated schema —
 * the app/Http/Controllers/Admin/* code that uses them predates the later
 * Modules/* refactor that introduced registration_id-based linking, and was
 * never updated. Rather than rewrite that admin functionality (attendance
 * scanning, finance/payment approval, category descriptions) against the new
 * registration-centric tables, this restores the columns those controllers
 * already read/write so the existing code works against real columns.
 */
class AlignLegacyModelColumns extends Migration
{
    public function up()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->nullable()->after('id');
            $table->unsignedBigInteger('participant_id')->nullable()->after('event_id');
            $table->unsignedBigInteger('invitation_id')->nullable()->after('participant_id');
            $table->string('type', 20)->nullable()->after('invitation_id');
            $table->dateTime('scanned_at')->nullable()->after('type');
            $table->string('device_info', 255)->nullable()->after('scanned_at');
            $table->string('ip_address', 45)->nullable()->after('device_info');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->nullable()->after('registration_id');
            $table->unsignedBigInteger('participant_id')->nullable()->after('event_id');
            $table->string('method', 50)->nullable()->after('participant_id');
            $table->string('proof_file', 255)->nullable()->after('method');
            $table->unsignedBigInteger('approved_by')->nullable()->after('proof_file');
            $table->dateTime('approved_at')->nullable()->after('approved_by');
        });

        Schema::table('event_categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dateTime('generated_at')->nullable();
        });

        // Backfill event_id/participant_id for existing rows via their registration.
        DB::statement('
            UPDATE payments p
            INNER JOIN registrations r ON r.id = p.registration_id
            SET p.event_id = r.event_id, p.participant_id = r.participant_id
            WHERE p.registration_id IS NOT NULL
        ');

        DB::statement('
            UPDATE attendance_logs al
            INNER JOIN registrations r ON r.id = al.registration_id
            SET al.event_id = r.event_id, al.participant_id = r.participant_id
            WHERE al.registration_id IS NOT NULL
        ');
    }

    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn(['event_id', 'participant_id', 'invitation_id', 'type', 'scanned_at', 'device_info', 'ip_address']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['event_id', 'participant_id', 'method', 'proof_file', 'approved_by', 'approved_at']);
        });

        Schema::table('event_categories', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('generated_at');
        });
    }
}
