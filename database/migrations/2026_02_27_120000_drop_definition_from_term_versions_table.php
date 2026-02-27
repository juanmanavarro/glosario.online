<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('term_versions', function (Blueprint $table) {
            if (Schema::hasColumn('term_versions', 'definition')) {
                $table->dropColumn('definition');
            }
        });
    }

    public function down(): void
    {
        Schema::table('term_versions', function (Blueprint $table) {
            if (! Schema::hasColumn('term_versions', 'definition')) {
                $table->longText('definition')->after('title');
            }
        });
    }
};
