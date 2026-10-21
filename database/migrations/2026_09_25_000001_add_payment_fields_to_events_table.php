<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('allow_certificate');
            $table->boolean('is_internal')->default(false)->after('is_paid');
            $table->decimal('price', 12, 2)->default(0)->after('is_internal');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'is_internal', 'price']);
        });
    }
}
