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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->boolean('is_active')->default(false);
            
            // Generated Virtual Column for Multi-Column Soft Delete Unique Constraint
            $table->string('active_semester', 50)
                ->virtualAs("CASE WHEN deleted_at IS NULL THEN CONCAT(academic_year_id, '-', semester) ELSE NULL END")
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Key and Index Definitions
            $table->foreign('academic_year_id')
                ->references('id')
                ->on('academic_years')
                ->onDelete('restrict');

            $table->unique('active_semester', 'uq_semesters_active_semester');
            $table->index('is_active', 'idx_semesters_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
