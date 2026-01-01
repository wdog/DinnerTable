<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource;

/**
 * Pagina custom per visualizzare i dettagli completi di una disponibilità.
 *
 * Questa classe estende ViewRecord per creare una pagina di visualizzazione
 * personalizzata con layout custom e logica avanzata.
 *
 * Caratteristiche principali:
 * - Vista Blade completamente personalizzata (non usa il form standard)
 * - Eager loading ottimizzato delle relazioni
 * - Azioni header condizionali (Edit solo per date future)
 * - Statistiche dettagliate per gli host
 * - Lista prenotazioni ricevute
 *
 * Architettura:
 * - Estende ViewRecord (non Page) per avere routing automatico via Resource
 * - Il record viene caricato automaticamente da Filament e reso disponibile in $this->record
 * - La vista è accessibile tramite route: /dinner/dinner-availabilities/{id}/show
 *
 * @see DinnerAvailabilityResource::getPages() Registrazione route 'show'
 * @see DinnerAvailabilitiesTable::recordAction() Click riga apre questa pagina
 */
class ShowDinnerAvailability extends ViewRecord
{
    /**
     * Resource Filament associato.
     *
     * Questa property collega la pagina al DinnerAvailabilityResource,
     * permettendo a Filament di generare automaticamente le route e
     * risolvere il record dal parametro URL.
     */
    protected static string $resource = DinnerAvailabilityResource::class;

    /**
     * Path della vista Blade custom.
     *
     * IMPORTANTE: Questa property è protected (NON static) perché così
     * è definita in ViewRecord. Se fosse static darebbe errore.
     *
     * La vista sostituisce completamente il form standard di Filament,
     * permettendo un layout personalizzato con sezioni condizionali.
     *
     * @var string Path relativo alla vista (resources/views/)
     */
    protected string $view = 'filament.app.resources.dinner-availabilities.pages.show-dinner-availability';

    /**
     * Personalizza l'heading della pagina.
     *
     * Questo metodo sovrascrive l'heading di default di Filament
     * per mostrare la data della cena formattata in italiano.
     *
     * Formato output: "Cena di lunedì, 23 gennaio 2025"
     *
     * L'heading appare come titolo principale della pagina,
     * sopra il contenuto e sotto la topbar.
     *
     * @return string Heading formattato
     */
    public function getHeading(): string
    {
        return 'Cena di ' . $this->record->dinnerDate->dinner_date->isoFormat('dddd, D MMMM YYYY');
    }

    /**
     * Override del metodo di caricamento record per eager loading.
     *
     * Questo metodo viene chiamato automaticamente da Filament quando
     * la pagina viene montata. Carica il record e le sue relazioni
     * per evitare query N+1 nella vista.
     *
     * Relazioni caricate:
     * - dinnerDate: data della cena
     * - user.profile: dati utente che ha creato la disponibilità
     * - bookings.guest.profile: prenotazioni con dati degli ospiti
     *
     * Il record caricato è poi disponibile nella vista come $record
     * e nella classe come $this->record.
     *
     * @param  int|string  $key  ID del record da caricare (dal parametro route)
     * @return \Illuminate\Database\Eloquent\Model Record con relazioni caricate
     */
    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return parent::resolveRecord($key)->load([
            'dinnerDate',
            'user.profile',
            'bookings.guest.profile',
            'logs.user', // Eager load logs con utenti per timeline
        ]);
    }

    /**
     * Definisce le azioni mostrate nell'header della pagina.
     *
     * Le azioni sono bottoni che appaiono in alto a destra nella pagina.
     * Filament gestisce automaticamente il routing e le autorizzazioni.
     *
     * Logica condizionale:
     * - EditAction: mostrata SOLO se la data è futura
     *   (non ha senso modificare eventi passati o completati)
     * - DeleteAction: sempre mostrata, ma controllata da policy
     *   (DinnerAvailabilityPolicy::delete verifica permessi)
     *
     * Le azioni usano automaticamente:
     * - Il record corrente ($this->record)
     * - Le route del Resource (edit, index)
     * - Le policy configurate
     * - I redirect post-azione
     *
     * @return array<\Filament\Actions\Action> Array di azioni Filament
     */
    protected function getHeaderActions(): array
    {
        $actions = [];

        // EditAction solo se data futura
        // Filament genera automaticamente l'URL verso la pagina 'edit'
        if ($this->record->dinnerDate->dinner_date->isFuture()) {
            $actions[] = EditAction::make();
        }

        // DeleteAction con policy check automatica
        // Dopo delete, Filament redirige automaticamente alla lista (index)
        $actions[] = DeleteAction::make();

        return $actions;
    }
}
