<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration per la creazione della tabella dei gruppi cena.
 *
 * Crea la tabella 'dinner_groups' che contiene informazioni sui gruppi
 * di utenti che partecipano alle cene condivise.
 */
return new class extends Migration
{
    /**
     * Esegue la migration.
     *
     * Crea la tabella 'dinner_groups' con i seguenti campi:
     * - name: nome del gruppo
     * - group_code: codice univoco per unirsi al gruppo (14 caratteri)
     * - slogan: slogan opzionale del gruppo
     * - group_image: immagine opzionale del gruppo
     * - created_by: riferimento all'utente creatore del gruppo
     */
    public function up(): void
    {
        Schema::create('dinner_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('group_code', 14)->unique();
            $table->string('slogan')->nullable();
            $table->string('group_image')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Annulla la migration.
     *
     * Elimina la tabella 'dinner_groups' dal database.
     */
    public function down(): void
    {
        Schema::dropIfExists('dinner_groups');
    }
};
