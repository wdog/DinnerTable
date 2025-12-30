<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 0-5
            $table->text('comment')->nullable();
            $table->timestamps();

            // Un utente può lasciare una sola recensione
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_reviews');
    }
};
