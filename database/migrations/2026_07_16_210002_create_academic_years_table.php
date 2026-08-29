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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 9); // Format YYYY/YYYY (e.g. 2026/2027)
            $table->boolean('is_active')->default(false);
            
            // Generated Virtual Column for Soft Delete Unique Constraint
            $table->string('active_name', 9)
                ->virtualAs("CASE WHEN deleted_at IS NULL THEN name ELSE NULL END")
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index Definitions
            $table->unique('active_name', 'uq_academic_years_active_name');
            $table->index('is_active', 'idx_academic_years_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
