<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial data
        $categories = [
            ['name' => 'Rapat Internal', 'slug' => 'rapat', 'description' => 'Kegiatan rapat internal organisasi/kampus'],
            ['name' => 'Seminar', 'slug' => 'seminar', 'description' => 'Seminar akademik atau umum'],
            ['name' => 'Workshop', 'slug' => 'workshop', 'description' => 'Kegiatan pelatihan dan workshop'],
            ['name' => 'Wisuda', 'slug' => 'wisuda', 'description' => 'Acara wisuda kampus'],
            ['name' => 'Kuliah Umum', 'slug' => 'kuliah-umum', 'description' => 'Kuliah umum dengan pembicara tamu'],
            ['name' => 'Lomba', 'slug' => 'lomba', 'description' => 'Perlombaan atau kompetisi'],
        ];

        foreach ($categories as $category) {
            DB::table('event_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Add category_id to events table if it doesn't exist
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'event_category_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreignId('event_category_id')->nullable()->after('event_type')->constrained('event_categories')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'event_category_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropForeign(['event_category_id']);
                $table->dropColumn('event_category_id');
            });
        }
        Schema::dropIfExists('event_categories');
    }
};
