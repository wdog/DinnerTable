<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
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
