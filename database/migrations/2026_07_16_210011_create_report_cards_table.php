<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignid('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignid('academic_class_id')->constrained('classes')->cascadeOnDelete();
            
            $table->integer('total_sick')->default(0);
            $table->integer('total_permission')->default(0);
            $table->integer('total_absent')->default(0);
            
            $table->text('homeroom_notes')->nullable();
            
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            
            $table->unique(['student_id', 'semester_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_cards');
    }
};
