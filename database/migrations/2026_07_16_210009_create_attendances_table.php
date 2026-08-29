<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignid('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignid('academic_class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['H', 'I', 'S', 'A'])->comment('Hadir, Izin, Sakit, Alpa');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
