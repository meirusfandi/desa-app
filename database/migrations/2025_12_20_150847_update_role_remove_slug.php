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
        $tableNames = config('permission.table_names');
        $rolesTable = $tableNames['roles'];

        Schema::table($rolesTable, function (Blueprint $table) use ($rolesTable) {
            if (Schema::hasColumn($rolesTable, 'slug')) {
                $table->dropColumn('slug');
            }

            if (! Schema::hasColumn($rolesTable, 'role_name')) {
                $table->string('role_name')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        $rolesTable = $tableNames['roles'];

        Schema::table($rolesTable, function (Blueprint $table) use ($rolesTable) {
            if (Schema::hasColumn($rolesTable, 'role_name')) {
                $table->dropColumn('role_name');
            }

            if (! Schema::hasColumn($rolesTable, 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
        });
    }
};
