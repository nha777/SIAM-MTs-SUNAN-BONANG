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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('guardian_name', 150);
            $table->enum('guardian_relation', ['ayah', 'ibu', 'paman_bibi', 'kakek_nenek', 'lainnya'])->default('ayah');
            $table->string('phone_number', 20);
            $table->text('address')->nullable();
            
            // Generated Virtual Column for MariaDB Soft Delete Unique Constraint
            $table->string('active_phone_number', 20)
                ->virtualAs("CASE WHEN deleted_at IS NULL THEN phone_number ELSE NULL END")
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Key and Index Definitions
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->unique('active_phone_number', 'uq_guardians_active_phone');
            $table->index('guardian_name', 'idx_guardians_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
