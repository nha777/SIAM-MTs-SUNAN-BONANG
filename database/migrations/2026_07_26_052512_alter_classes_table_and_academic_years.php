<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update academic_years table
        Schema::table('academic_years', function (Blueprint $table) {
            $table->year('start_year')->nullable()->after('name');
            $table->year('end_year')->nullable()->after('start_year');
        });

        // 2. Update classes table
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'semester_id')) {
                $table->unsignedBigInteger('semester_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('classes', 'academic_year_id')) {
                $table->unsignedBigInteger('academic_year_id')->nullable()->after('semester_id');
            }

            if (!Schema::hasColumn('classes', 'capacity')) {
                $table->integer('capacity')->default(32)->after('grade');
            }

            if (!Schema::hasColumn('classes', 'display_order')) {
                $table->integer('display_order')->nullable()->after('capacity');
            }

            if (!Schema::hasColumn('classes', 'active_class_key')) {
                $table->string('active_class_key')->virtualAs("CASE WHEN deleted_at IS NULL THEN CONCAT(COALESCE(academic_year_id, 0), '-', grade, '-', name) ELSE NULL END")->nullable()->after('grade');
            }

            if (!Schema::hasColumn('classes', 'active_class_name')) {
                $table->string('active_class_name', 100)
                    ->virtualAs("CASE WHEN deleted_at IS NULL THEN CONCAT(COALESCE(semester_id, 0), '-', name) ELSE NULL END")
                    ->nullable()->after('grade');
            }
        });

        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'semester_id')) {
                $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('restrict');
            }

            if (Schema::hasColumn('classes', 'academic_year_id')) {
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('restrict');
            }

            if (!Schema::getConnection()->getSchemaBuilder()->hasIndex('classes', 'uq_classes_active_class_key')) {
                $table->unique('active_class_key', 'uq_classes_active_class_key');
            }

            if (!Schema::getConnection()->getSchemaBuilder()->hasIndex('classes', 'uq_classes_active_class_name')) {
                $table->unique('active_class_name', 'uq_classes_active_class_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
            }
            if (Schema::hasColumn('classes', 'semester_id')) {
                $table->dropForeign(['semester_id']);
            }
            if (Schema::hasColumn('classes', 'active_class_key')) {
                $table->dropUnique('uq_classes_active_class_key');
                $table->dropColumn('active_class_key');
            }
            if (Schema::hasColumn('classes', 'active_class_name')) {
                $table->dropUnique('uq_classes_active_class_name');
                $table->dropColumn('active_class_name');
            }
            if (Schema::hasColumn('classes', 'academic_year_id')) {
                $table->dropColumn('academic_year_id');
            }
            if (Schema::hasColumn('classes', 'capacity')) {
                $table->dropColumn('capacity');
            }
            if (Schema::hasColumn('classes', 'display_order')) {
                $table->dropColumn('display_order');
            }
        });

        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'semester_id')) {
                $table->unsignedBigInteger('semester_id')->after('id');
            }
            $table->string('active_class_name', 100)
                ->virtualAs("CASE WHEN deleted_at IS NULL THEN CONCAT(semester_id, '-', name) ELSE NULL END")
                ->nullable();
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('restrict');
            $table->unique('active_class_name', 'uq_classes_active_class_name');
        });

        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn(['start_year', 'end_year']);
        });
    }
};
