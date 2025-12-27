<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dinner_availabilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dinner_date_id')
                ->constrained('dinner_dates')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('status'); // available | unavailable | maybe
            $table->boolean('can_host')->default(false);
            $table->unsignedInteger('max_guests')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
            $table->unique(['dinner_date_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dinner_availabilities');
    }
};
