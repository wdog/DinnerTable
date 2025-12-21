<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dinner_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dinner_group_id')
                ->constrained('dinner_groups')
                ->cascadeOnDelete();

            $table->date('dinner_date');
            $table->boolean('is_closed')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['dinner_group_id', 'dinner_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dinner_dates');
    }
};
