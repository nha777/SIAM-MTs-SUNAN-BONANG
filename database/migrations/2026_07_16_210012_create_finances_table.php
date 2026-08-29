<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Kategori / Template Tagihan
        Schema::create('billing_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('default_amount', 15, 2)->default(0);
            $table->string('frequency')->default('One-time')->comment('One-time, Monthly, Yearly');
            $table->timestamps();
        });

        // Tagihan
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignUuid('billing_category_id')->nullable()->constrained('billing_categories')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('status')->default('Unpaid')->comment('Unpaid, Partial, Paid');
            $table->date('due_date');
            $table->timestamps();
        });

        // Pembayaran
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('payment_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('Tunai');
            $table->string('reference_number')->nullable();
            $table->string('proof_of_payment')->nullable()->comment('Path to uploaded file');
            $table->string('status')->default('Verified')->comment('Pending, Verified, Rejected');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignid('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('billing_categories');
    }
};
