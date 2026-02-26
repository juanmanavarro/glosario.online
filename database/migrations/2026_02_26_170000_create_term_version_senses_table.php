<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_version_senses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_version_id')->constrained('term_versions')->cascadeOnDelete();
            $table->unsignedInteger('sense_number');
            $table->longText('definition');
            $table->timestamps();

            $table->unique(['term_version_id', 'sense_number']);
            $table->index('term_version_id');
        });

        $now = now();

        $rows = DB::table('term_versions')
            ->select(['id', 'definition', 'created_at', 'updated_at'])
            ->whereNotNull('definition')
            ->get()
            ->map(fn ($version) => [
                'term_version_id' => $version->id,
                'sense_number' => 1,
                'definition' => (string) $version->definition,
                'created_at' => $version->created_at ?? $now,
                'updated_at' => $version->updated_at ?? $now,
            ])
            ->all();

        if ($rows !== []) {
            DB::table('term_version_senses')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('term_version_senses');
    }
};
