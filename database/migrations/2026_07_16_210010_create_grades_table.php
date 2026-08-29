<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignid('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignid('academic_class_id')->constrained('classes')->cascadeOnDelete();
            
            // Komponen Nilai
            $table->decimal('assignment_score', 5, 2)->default(0);
            $table->decimal('mid_exam_score', 5, 2)->default(0);
            $table->decimal('final_exam_score', 5, 2)->default(0);
            $table->decimal('final_score', 5, 2)->default(0);
            $table->string('predicate', 2)->nullable(); // A, B, C, D
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->unique(['student_id', 'subject_id', 'semester_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grades');
    }
};
