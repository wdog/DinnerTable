<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration per la tabella dinner_logs.
 *
 * Crea una tabella per tracciare cronologicamente tutti gli eventi
 * relativi alle disponibilità (e future prenotazioni) creando un audit trail immutabile.
 *
 * Caratteristiche:
 * - Polymorphic relation (loggable) per estendibilità a DinnerBooking
 * - logged_by NULLABLE per eventi di sistema (cron job)
 * - availability_id sempre presente per query dirette
 * - metadata JSON per dati flessibili
 * - Solo created_at (record immutabili)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dinner_logs', function (Blueprint $table) {
            $table->id();

            // NULLABLE: Chi ha causato l'evento (null per eventi di sistema come cron job)
            $table->foreignId('logged_by')->nullable()->constrained('users')->nullOnDelete();

            // Polymorphic relation (per futura estensione a bookings in Part 2)
            $table->morphs('loggable'); // crea loggable_type e loggable_id

            // Availability reference sempre presente per query facili
            $table->foreignId('availability_id')->constrained('dinner_availabilities')->cascadeOnDelete();

            // Status DOPO l'evento (enum value string)
            $table->string('status');

            // Dati aggiuntivi opzionali (JSON per flessibilità)
            $table->json('metadata')->nullable();

            // Solo created_at (record immutabili, no updated_at)
            $table->timestamp('created_at');

            // Indici per performance
            $table->index('availability_id');
            $table->index('logged_by');
            // morphs() già crea l'indice per loggable_type + loggable_id
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dinner_logs');
    }
};
