<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_requests', function (Blueprint $table) {
            $table->timestamp('signed_at')->nullable()->after('signed_file');
        });
    }

    public function down(): void
    {
        Schema::table('surat_requests', function (Blueprint $table) {
            $table->dropColumn('signed_at');
        });
    }
};
