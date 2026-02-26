<?php

use App\Enums\TermRelationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('term_relations');
    }

    public function down(): void
    {
        Schema::create('term_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->foreignId('related_term_id')->constrained('terms')->cascadeOnDelete();
            $table->enum('relation_type', array_column(TermRelationType::cases(), 'value'));
            $table->timestamps();

            $table->unique(['term_id', 'related_term_id', 'relation_type']);
            $table->index('term_id');
            $table->index('related_term_id');
            $table->index('relation_type');
        });
    }
};
