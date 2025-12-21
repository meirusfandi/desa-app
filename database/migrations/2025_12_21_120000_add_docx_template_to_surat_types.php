<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_types', function (Blueprint $table) {
            $table->string('template_doc_path')->nullable()->after('template_html');
            $table->string('template_doc_original_name')->nullable()->after('template_doc_path');
            $table->longText('template_html')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('surat_types', function (Blueprint $table) {
            $table->dropColumn(['template_doc_path', 'template_doc_original_name']);
            $table->longText('template_html')->nullable(false)->change();
        });
    }
};
