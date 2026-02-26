<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->string('language_code', 5);
            $table->string('title');
            $table->longText('definition');
            $table->longText('etymology')->nullable();
            $table->longText('notes')->nullable();
            $table->longText('bibliography')->nullable();
            $table->unsignedInteger('version_number');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['term_id', 'language_code', 'version_number']);
            $table->index(['term_id', 'language_code']);
            $table->index('language_code');
            // Optional FULLTEXT(title, definition) can be added on MySQL if needed.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term_versions');
    }
};
