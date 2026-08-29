<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            if (!Schema::hasColumn('semesters', 'name')) {
                $table->string('name')->nullable()->after('academic_year_id');
            }
        });

        DB::table('semesters')->whereNull('name')->orWhere('name', '')->chunkById(100, function ($semesters) {
            foreach ($semesters as $semester) {
                $value = !empty($semester->semester) ? ucfirst((string) $semester->semester) : 'Semester';
                DB::table('semesters')->where('id', $semester->id)->update(['name' => $value]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            if (Schema::hasColumn('semesters', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
