<?php

namespace App\Filament\App\Resources\DinnerAvailabilities;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Models\DinnerAvailability;
use Illuminate\Support\Facades\Auth;
use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\EditDinnerAvailability;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\ShowDinnerAvailability;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\ViewDinnerAvailability;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\CreateDinnerAvailability;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\ListDinnerAvailabilities;
use App\Filament\App\Resources\DinnerAvailabilities\Schemas\DinnerAvailabilityForm;
use App\Filament\App\Resources\DinnerAvailabilities\Tables\DinnerAvailabilitiesTable;

/**
 * Risorsa Filament per la gestione delle disponibilità degli utenti.
 *
 * Questa risorsa permette agli utenti di dichiarare la propria disponibilità
 * per una data specifica, indicando se possono ospitare (host) oppure se
 * vogliono partecipare come ospiti (guest).
 *
 * Funzionalità principali:
 * - Visualizza solo le disponibilità dell'utente autenticato
 * - Filtra le disponibilità per il gruppo dell'utente
 * - Gestisce stati automatici basati su prenotazioni (solo per host)
 *
 * @see DinnerAvailability Model principale
 * @see DinnerAvailabilityForm Form per creare/modificare disponibilità
 * @see DinnerAvailabilitiesTable Tabella con lista disponibilità
 */
class DinnerAvailabilityResource extends Resource
{
    /**
     * Modello Eloquent associato a questa risorsa.
     */
    protected static ?string $model = DinnerAvailability::class;

    /**
     * Icona mostrata nel menu di navigazione.
     */
    protected static string|BackedEnum|null $navigationIcon = 'tabler-home-2';

    /**
     * Attributo del modello usato come titolo del record.
     */
    protected static ?string $recordTitleAttribute = 'Dinner Date';

    /**
     * Label mostrata nel menu di navigazione.
     */
    protected static ?string $navigationLabel = 'Le mie Disponibilità';

    protected static ?string $modelLabel = 'La mia disponibilità';

    protected static ?string $pluralModelLabel = 'Le mie disponibilità';

    /**
     * Gruppo di navigazione in cui viene mostrata la risorsa.
     */
    protected static string|UnitEnum|null $navigationGroup = 'Gestione Cene';

    protected static ?int $navigationSort = 3;

    /**
     * Configura la query Eloquent per questa risorsa.
     *
     * Filtra i record per mostrare solo:
     * - Le disponibilità dell'utente autenticato
     * - Le disponibilità del gruppo a cui appartiene l'utente
     *
     * Questo garantisce che ogni utente veda solo le proprie disponibilità
     * e non quelle di altri membri del gruppo.
     *
     * @return Builder Query Eloquent filtrata
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::user()->id)
            ->whereHas('dinnerDate', fn ($q) => $q->where('dinner_group_id', Auth::user()->dinner_group_id));
    }

    /**
     * Configura lo schema del form per creare/modificare disponibilità.
     *
     * Delega la configurazione al FormSchema dedicato che gestisce:
     * - Selezione della data
     * - Toggle per indicare se si vuole ospitare
     * - Selezione dello stato (filtrato in base a can_host)
     * - Numero massimo ospiti (solo per host)
     * - Note opzionali
     *
     * Form disabilitato automaticamente se:
     * - Stato COMPLETED (cena conclusa)
     * - Data nel passato
     *
     * @param  Schema  $schema  Schema Filament da configurare
     * @return Schema Schema configurato con i campi del form
     *
     * @see DinnerAvailabilityForm::configure()
     */
    public static function form(Schema $schema): Schema
    {
        $schema = DinnerAvailabilityForm::configure($schema);

        // Disabilita il form se è read-only (completato o passato)
        $schema->disabled(function ($record) {
            if ( ! $record) {
                return false; // Form di creazione sempre abilitato
            }

            // Completata = read-only
            if ($record->status === DinnerAvailabilityStatus::COMPLETED) {
                return true;
            }

            // Data passata = read-only
            if ($record->dinnerDate && $record->dinnerDate->dinner_date < today()) {
                return true;
            }

            return false;
        });

        return $schema;
    }

    /**
     * Configura la tabella per visualizzare la lista delle disponibilità.
     *
     * Delega la configurazione alla TableSchema dedicata che gestisce:
     * - Colonne per visualizzare data, stato, tipo (host/guest)
     * - Filtri per stato e data
     * - Azioni per modificare/eliminare disponibilità
     *
     * @param  Table  $table  Tabella Filament da configurare
     * @return Table Tabella configurata con colonne e azioni
     *
     * @see DinnerAvailabilitiesTable::configure()
     */
    public static function table(Table $table): Table
    {
        return DinnerAvailabilitiesTable::configure($table);
    }

    /**
     * Definisce le relazioni Filament disponibili per questa risorsa.
     *
     * Relation managers configurati:
     * - BookingsRelationManager: Gestione prenotazioni ricevute (solo per host)
     *
     * @return array Array di relation managers
     */
    public static function getRelations(): array
    {
        return [
            RelationManagers\BookingsRelationManager::class,
        ];
    }

    /**
     * Definisce le pagine disponibili per questa risorsa.
     *
     * Pagine configurate:
     * - index: Lista di tutte le disponibilità dell'utente
     * - create: Form per creare una nuova disponibilità
     * - view: Visualizzazione dettaglio in sola lettura (standard)
     * - show: Visualizzazione dettaglio custom con layout avanzato
     * - edit: Form per modificare una disponibilità esistente
     *
     * @return array Array associativo di route per le pagine
     */
    public static function getPages(): array
    {
        return [
            'index'  => ListDinnerAvailabilities::route('/'),
            'create' => CreateDinnerAvailability::route('/create'),
            'view'   => ViewDinnerAvailability::route('/{record}'),
            'show'   => ShowDinnerAvailability::route('/{record}/show'),
            'edit'   => EditDinnerAvailability::route('/{record}/edit'),
        ];
    }
}
