<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->string('keyword');
            $table->timestamps();

            $table->unique(['term_id', 'keyword']);
            $table->index('keyword');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
