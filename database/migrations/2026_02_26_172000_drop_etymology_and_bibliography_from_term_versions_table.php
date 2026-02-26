<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('term_versions', function (Blueprint $table) {
            if (Schema::hasColumn('term_versions', 'etymology')) {
                $table->dropColumn('etymology');
            }

            if (Schema::hasColumn('term_versions', 'bibliography')) {
                $table->dropColumn('bibliography');
            }
        });
    }

    public function down(): void
    {
        Schema::table('term_versions', function (Blueprint $table) {
            if (! Schema::hasColumn('term_versions', 'etymology')) {
                $table->longText('etymology')->nullable()->after('definition');
            }

            if (! Schema::hasColumn('term_versions', 'bibliography')) {
                $table->longText('bibliography')->nullable()->after('notes');
            }
        });
    }
};
