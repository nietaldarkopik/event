<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationAndParticipationTables extends Migration
{
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->char('uuid', 36)->nullable();
            $table->string('full_name', 200)->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('institution', 255)->nullable();
            $table->string('profession', 255)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('photo', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->unsignedBigInteger('ticket_type_id')->nullable();
            $table->string('registration_number', 50)->nullable();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'CANCELLED', 'ATTENDED'])->default('PENDING');
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')->onDelete('set null');

            $table->index('event_id', 'idx_registration_event');
            $table->index('participant_id', 'idx_registration_participant');
            $table->index('status', 'idx_registration_status');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_id')->nullable();
            $table->string('invoice_number', 100)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_gateway', 50)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->enum('status', ['UNPAID', 'PAID', 'FAILED', 'REFUND'])->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');

            $table->index('status', 'idx_payment_status');
        });

        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_id')->nullable();
            $table->string('code', 255)->unique();
            $table->dateTime('expired_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamp('created_at')->nullable();

            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');

            $table->index('code', 'idx_qrcode_code');
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_id')->nullable();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('scanner_user_id')->nullable();
            $table->string('device', 100)->nullable();
            $table->dateTime('scan_time')->nullable();
            $table->enum('status', ['SUCCESS', 'DUPLICATE', 'INVALID'])->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('event_sessions')->onDelete('set null');
            $table->foreign('scanner_user_id')->references('id')->on('users')->onDelete('set null');

            $table->index('scan_time', 'idx_attendance_scan');
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
            $table->foreignId('participant_id')->nullable()->constrained('participants')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('certificate_templates')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('certificate_categories')->onDelete('cascade');
            $table->unsignedBigInteger('registration_id')->nullable();
            $table->string('certificate_number', 100)->nullable();
            $table->string('unique_code', 100)->nullable()->unique();
            $table->string('certificate_hash', 255)->nullable()->unique();
            $table->string('pdf_path', 255)->nullable();
            $table->string('qr_code_path', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_revoked')->default(false);
            $table->dateTime('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('revoke_reason')->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('downloaded_at')->nullable();
            $table->timestamps();

            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('qr_codes');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('participants');
    }
}
