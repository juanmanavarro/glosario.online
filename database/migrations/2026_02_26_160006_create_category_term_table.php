<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_term', function (Blueprint $table) {
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();

            $table->primary(['term_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_term');
    }
};
