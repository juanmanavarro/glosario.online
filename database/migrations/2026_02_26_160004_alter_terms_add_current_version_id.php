<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            $table->foreignId('current_version_id')
                ->nullable()
                ->after('status')
                ->constrained('term_versions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_version_id');
        });
    }
};
