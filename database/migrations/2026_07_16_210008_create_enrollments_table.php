<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignid('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignid('academic_class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('enrollment_date');
            $table->string('status')->default('Aktif')->comment('Aktif, Pindah, Lulus, Keluar');
            $table->timestamps();
            
            // A student can only be enrolled in one class per semester
            $table->unique(['student_id', 'academic_year_id', 'semester_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
};
