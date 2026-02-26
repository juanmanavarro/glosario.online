<?php

use App\Enums\SenseRelationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_version_sense_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_version_sense_id')->constrained('term_version_senses')->cascadeOnDelete();
            $table->foreignId('related_term_id')->constrained('terms')->cascadeOnDelete();
            $table->enum('relation_type', array_column(SenseRelationType::cases(), 'value'));
            $table->timestamps();

            $table->unique(['term_version_sense_id', 'related_term_id', 'relation_type'], 'sense_rel_unique');
            $table->index('term_version_sense_id');
            $table->index('related_term_id');
            $table->index('relation_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term_version_sense_relations');
    }
};
