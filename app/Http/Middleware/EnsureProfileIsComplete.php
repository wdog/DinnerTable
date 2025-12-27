<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware per garantire che il profilo utente sia completo.
 *
 * Questo middleware intercetta tutte le richieste al pannello app e verifica
 * che l'utente abbia completato il proprio profilo. Se il profilo è incompleto,
 * l'utente viene reindirizzato alla pagina di completamento profilo.
 *
 * Funzionalità:
 * - Crea automaticamente un profilo vuoto se non esiste
 * - Verifica che tutti i campi obbligatori siano compilati
 * - Reindirizza alla pagina di completamento se necessario
 * - Permette agli admin di bypassare il controllo
 *
 * Campi obbligatori del profilo:
 * - Città, indirizzo, numero civico, CAP
 * - Numero massimo ospiti
 * - Accettazione privacy
 *
 * Eccezioni:
 * - Admin possono accedere senza profilo completo
 * - Utenti non autenticati passano senza controllo
 * - La pagina di completamento profilo stessa è esclusa (evita loop)
 *
 * Registrazione:
 * Il middleware viene registrato in AppPanelProvider per il pannello app.
 *
 * @see User::hasCompletedProfile()
 * @see Profile::isComplete()
 * @see \App\Filament\App\Pages\CompleteProfile
 */
class EnsureProfileIsComplete
{
    /**
     * Gestisce una richiesta HTTP in entrata.
     *
     * Verifica lo stato del profilo utente e reindirizza alla pagina
     * di completamento se necessario.
     *
     * Flusso logico:
     * 1. Se utente non autenticato o admin → passa
     * 2. Se profilo non esiste → lo crea vuoto
     * 3. Se profilo incompleto E non nella pagina di completamento → reindirizza
     * 4. Altrimenti → passa alla richiesta successiva
     *
     * @param  Request  $request  Richiesta HTTP in entrata
     * @param  Closure  $next  Closure per passare alla prossima middleware/controller
     * @return Response Risposta HTTP (redirect o next)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se l'utente non è autenticato o è admin, lascia passare
        if ( ! $user || $user->is_admin) {
            return $next($request);
        }

        // Se non ha un profilo, crealo
        if ( ! $user->profile) {
            $user->profile()->create();
        }

        // Se il profilo non è completo, reindirizza alla pagina di completamento
        if ( ! $user->hasCompletedProfile() && ! $request->routeIs('filament.dinner.pages.complete-profile')) {
            return redirect()->route('filament.dinner.pages.complete-profile');
        }

        return $next($request);
    }
}
