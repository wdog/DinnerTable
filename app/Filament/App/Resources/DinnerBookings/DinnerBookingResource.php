<?php

namespace App\Filament\App\Resources\DinnerBookings;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\DinnerBooking;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\App\Resources\DinnerBookings\Pages\EditDinnerBooking;
use App\Filament\App\Resources\DinnerBookings\Pages\ListDinnerBookings;
use App\Filament\App\Resources\DinnerBookings\Schemas\DinnerBookingForm;
use App\Filament\App\Resources\DinnerBookings\Tables\DinnerBookingsTable;

/**
 * Risorsa Filament per la gestione delle proprie prenotazioni come guest.
 *
 * Permette all'utente di:
 * - Visualizzare tutte le sue prenotazioni
 * - Modificare dettagli (numero ospiti, items portati, note)
 * - Confermare o annullare la prenotazione tramite azioni rapide
 *
 * NON permette di creare prenotazioni (si fa da disponibilità).
 * Visualizza solo le prenotazioni dell'utente autenticato.
 *
 * @see DinnerBooking
 * @see DinnerBookingForm
 * @see DinnerBookingsTable
 */
class DinnerBookingResource extends Resource
{
    protected static ?string $model = DinnerBooking::class;

    protected static string|BackedEnum|null $navigationIcon = 'tabler-users-group';

    protected static ?string $navigationLabel = 'Le mie prenotazioni';

    protected static ?string $modelLabel = 'prenotazione';

    protected static ?string $pluralModelLabel = 'prenotazioni';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestione Cene';

    protected static ?int $navigationSort = 2;

    /**
     * Configura la query Eloquent per questa risorsa.
     *
     * Filtra le prenotazioni per mostrare solo quelle effettuate
     * dall'utente autenticato come guest, con eager loading delle
     * relazioni necessarie per ottimizzare le performance.
     *
     * Relazioni caricate:
     * - hostAvailability.user: Dati dell'host che ospita
     * - hostAvailability.dinnerDate: Data e gruppo della cena
     *
     * @return Builder Query Eloquent filtrata e ottimizzata
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('guest_user_id', Auth::id())
            ->with(['hostAvailability.user', 'hostAvailability.dinnerDate']);
    }

    /**
     * Configura lo schema del form per modificare prenotazioni.
     *
     * Delega la configurazione del form a DinnerBookingForm che gestisce:
     * - Numero di ospiti (guests_count)
     * - Oggetti/bevande portate (bringing_items)
     * - Note aggiuntive
     *
     * Il form viene automaticamente disabilitato (read-only) quando:
     * - Lo stato della disponibilità host non permette modifiche
     *   (es. COMPLETED, HOST_CANCELLED)
     * - La data della cena è nel passato
     *
     * Form di creazione:
     * La creazione di nuove prenotazioni non è disponibile da questa
     * risorsa. Le prenotazioni si creano dalla pagina GroupAvailabilities
     * o dalla disponibilità dell'host.
     *
     * @param  Schema  $schema  Schema Filament da configurare
     * @return Schema Schema configurato con logica di disabilitazione
     *
     * @see DinnerBookingForm::configure()
     * @see DinnerAvailabilityStatus::canUpdateBookings()
     */
    public static function form(Schema $schema): Schema
    {
        return DinnerBookingForm::configure($schema)
            ->disabled(function ($record) {
                if ( ! $record) {
                    return false;
                }

                // Disponibilità host non permette modifiche
                if ( ! $record->hostAvailability->status->canUpdateBookings()) {
                    return true;
                }

                // Data passata = read-only
                if ($record->hostAvailability->dinnerDate &&
                    $record->hostAvailability->dinnerDate->dinner_date < today()) {
                    return true;
                }

                return false;
            });
    }

    /**
     * Configura la tabella per visualizzare la lista delle prenotazioni.
     *
     * Delega la configurazione a DinnerBookingsTable che gestisce:
     * - Colonne per data, host, numero ospiti, stato
     * - Badge colorati per gli stati
     * - Filtri per stato e data
     * - Azioni per modificare/cancellare prenotazioni
     *
     * @param  Table  $table  Tabella Filament da configurare
     * @return Table Tabella configurata con colonne, filtri e azioni
     *
     * @see DinnerBookingsTable::configure()
     */
    public static function table(Table $table): Table
    {
        return DinnerBookingsTable::configure($table);
    }

    /**
     * Definisce le relazioni Filament disponibili per questa risorsa.
     *
     * Attualmente nessun relation manager è configurato per le prenotazioni.
     * Le relazioni sono visualizzate direttamente nella tabella principale.
     *
     * @return array Array vuoto di relation managers
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Definisce le pagine disponibili per questa risorsa.
     *
     * Pagine configurate:
     * - index: Lista di tutte le prenotazioni del guest
     * - edit: Form per modificare una prenotazione esistente
     *
     * Pagine NON disponibili:
     * - create: La creazione avviene dalla pagina GroupAvailabilities
     *   o dall'action "Prenota" su una disponibilità host
     * - view: Non necessaria, si usa direttamente edit in sola lettura
     *
     * @return array Array associativo di route per le pagine
     */
    public static function getPages(): array
    {
        return [
            'index' => ListDinnerBookings::route('/'),
            // 'create' => CreateDinnerBooking::route('/create'),
            'edit' => EditDinnerBooking::route('/{record}/edit'),
        ];
    }
}
