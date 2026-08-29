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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('semester_id');
            $table->string('name', 50); // e.g., "Kelas VII-A"
            $table->tinyInteger('grade')->unsigned(); // Jenjang MTs: 7, 8, 9
            
            // Generated Virtual Column for Soft Delete Unique Constraint on Name per Semester
            $table->string('active_class_name', 100)
                ->virtualAs("CASE WHEN deleted_at IS NULL THEN CONCAT(semester_id, '-', name) ELSE NULL END")
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Key and Index Definitions
            $table->foreign('semester_id')
                ->references('id')
                ->on('semesters')
                ->onDelete('restrict');

            $table->unique('active_class_name', 'uq_classes_active_class_name');
            $table->index('grade', 'idx_classes_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
