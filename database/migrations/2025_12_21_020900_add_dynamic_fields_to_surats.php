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
        Schema::table('surat_types', function (Blueprint $table) {
            $table->json('input_fields')->nullable()->after('required_documents');
        });

        Schema::table('surat_requests', function (Blueprint $table) {
            $table->json('data')->nullable()->after('surat_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_types', function (Blueprint $table) {
            $table->dropColumn('input_fields');
        });

        Schema::table('surat_requests', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }
};
