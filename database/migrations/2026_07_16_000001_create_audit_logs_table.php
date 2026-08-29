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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->uuid('event_id')->unique();
            $table->uuid('request_id')->index();
            $table->string('severity', 20)->default('info');
            
            // Polymorphic Actor (Catatan: Gunakan nullableUuidMorphs('actor') di masa depan jika migrasi ke UUID Primary Key)
            $table->nullableMorphs('actor'); // Menghasilkan actor_id (BIGINT UNSIGNED) dan actor_type (VARCHAR)
            
            $table->string('event_name', 100)->index(); // Diisi dari ENUM internal
            
            // Polymorphic Target (Auditable) (Catatan: Gunakan uuidMorphs('auditable') di masa depan jika migrasi ke UUID Primary Key)
            $table->morphs('auditable'); // Menghasilkan auditable_id (BIGINT UNSIGNED) dan auditable_type (VARCHAR)
            
            // Kolom JSON Portabel
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable(); // Struktur minimum wajib: {"actor_snapshot": {}, "roles": [], "request_context": {}, "extra": {}}
            
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
