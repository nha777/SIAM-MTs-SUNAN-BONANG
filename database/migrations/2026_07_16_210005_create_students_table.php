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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guardian_id')->nullable(); // Link to the new guardians table
            $table->unsignedBigInteger('class_id')->nullable(); // Class is nullable if newly registered
            $table->string('nisn', 10);
            $table->string('name', 150);
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->enum('status', ['aktif', 'lulus', 'mutasi', 'keluar', 'skorsing'])->default('aktif');
            
            // Generated Virtual Column for Soft Delete Unique Constraint on NISN
            $table->string('active_nisn', 10)
                ->virtualAs("CASE WHEN deleted_at IS NULL THEN nisn ELSE NULL END")
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Key and Index Definitions
            $table->foreign('guardian_id')
                ->references('id')
                ->on('guardians')
                ->onDelete('restrict');

            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->onDelete('set null');

            $table->unique('active_nisn', 'uq_students_active_nisn');
            $table->index('status', 'idx_students_status');
            $table->index('name', 'idx_students_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
