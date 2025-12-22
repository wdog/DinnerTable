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
        Schema::create('dinner_bookings', function (Blueprint $table) {
            $table->id();

            // Relazione con la disponibilità dell'host
            $table->foreignId('host_availability_id')
                ->constrained('dinner_availabilities')
                ->cascadeOnDelete();

            // Utente che prenota (guest)
            $table->foreignId('guest_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Dati della prenotazione
            $table->unsignedInteger('guests_count')->default(0); // Quanti ospiti porta oltre a se stesso
            $table->text('bringing_items')->nullable(); // Cosa porta
            $table->text('notes')->nullable(); // Note aggiuntive

            // Status della prenotazione
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');

            $table->timestamps();

            // Constraint: un utente non può prenotare due volte la stessa disponibilità
            $table->unique(['host_availability_id', 'guest_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dinner_bookings');
    }
};
