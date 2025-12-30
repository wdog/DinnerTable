<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AppReview;
use Illuminate\Database\Seeder;

/**
 * Seeder per generare recensioni random dell'applicazione.
 *
 * Crea recensioni casuali per circa il 70% degli utenti non admin.
 */
class AppReviewSeeder extends Seeder
{
    /**
     * Commenti di esempio per le recensioni.
     */
    private array $positiveComments = [
        'App fantastica! Mi ha semplificato molto l\'organizzazione delle cene.',
        'Finalmente un modo facile per coordinarsi con gli amici!',
        'Ottima app, molto intuitiva e pratica.',
        'Perfetta per gestire le cene di gruppo senza stress.',
        'Mi piace molto, finalmente niente più messaggi infiniti su WhatsApp!',
        'Utilissima! La consiglio a tutti i gruppi di amici.',
        'Interfaccia pulita e funzionalità utili.',
        'Facilita molto l\'organizzazione, ottimo lavoro!',
    ];

    private array $neutralComments = [
        'Buona app, ma si potrebbe migliorare la parte delle notifiche.',
        'Nel complesso funziona bene, qualche piccolo bug qua e là.',
        'Carina, ma vorrei più opzioni di personalizzazione.',
        'Fa il suo lavoro, anche se l\'interfaccia potrebbe essere più moderna.',
        'Utile, ma mancano alcune funzionalità che mi aspettavo.',
    ];

    private array $negativeComments = [
        'Qualche problema di usabilità, ma ha del potenziale.',
        'Idea buona ma l\'esecuzione lascia a desiderare.',
        'Troppo complicata per quello che fa.',
    ];

    public function run(): void
    {
        // Prendi tutti gli utenti non admin
        $users = User::where('is_admin', false)->get();

        // Genera recensioni per circa il 70% degli utenti
        $reviewCount = (int) ceil($users->count() * 0.7);

        $users->random($reviewCount)->each(function ($user) {
            $rating = $this->getRandomRating();

            AppReview::create([
                'user_id' => $user->id,
                'rating'  => $rating,
                'comment' => $this->getCommentForRating($rating),
            ]);
        });

        $this->command->info("Creato {$reviewCount} recensioni random.");
    }

    /**
     * Genera un rating casuale con probabilità realistica.
     */
    private function getRandomRating(): int
    {
        // Probabilità: più recensioni positive
        $probability = rand(1, 100);

        return match (true) {
            $probability <= 50 => 5, // 50% - 5 stelle
            $probability <= 75 => 4, // 25% - 4 stelle
            $probability <= 90 => 3, // 15% - 3 stelle
            $probability <= 97 => 2, // 7% - 2 stelle
            default            => 1,            // 3% - 1 stella
        };
    }

    /**
     * Seleziona un commento appropriato in base al rating.
     */
    private function getCommentForRating(int $rating): ?string
    {
        // 30% delle recensioni senza commento
        if (rand(1, 100) <= 30) {
            return null;
        }

        return match ($rating) {
            5, 4 => $this->positiveComments[array_rand($this->positiveComments)],
            3 => $this->neutralComments[array_rand($this->neutralComments)],
            2, 1 => $this->negativeComments[array_rand($this->negativeComments)],
            default => null,
        };
    }
}
