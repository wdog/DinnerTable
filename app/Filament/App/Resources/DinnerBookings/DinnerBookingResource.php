<?php

namespace App\Filament\App\Resources\DinnerBookings;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\DinnerBooking;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'Le mie prenotazioni';

    protected static ?string $modelLabel = 'prenotazione';

    protected static ?string $pluralModelLabel = 'prenotazioni';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestione Cene';

    protected static ?int $navigationSort = 2;

    /**
     * Filtra la query per mostrare solo le prenotazioni dell'utente autenticato.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('guest_user_id', Auth::id())
            ->with(['hostAvailability.user', 'hostAvailability.dinnerDate']);
    }

    /**
     * Configura il form per modificare prenotazioni.
     *
     * Form disabilitato automaticamente se:
     * - Prenotazione in stato CANCELLED
     * - Disponibilità host COMPLETED o HOST_CANCELLED
     * - Data nel passato
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function form(Schema $schema): Schema
    {
        return DinnerBookingForm::configure($schema)
            ->disabled(function ($record) {
                if (!$record) {
                    return false;
                }

                // Prenotazione cancellata = read-only
                if ($record->status->value === 'cancelled') {
                    return true;
                }

                // Disponibilità host non permette modifiche
                if (!$record->hostAvailability->status->canUpdateBookings()) {
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

    public static function table(Table $table): Table
    {
        return DinnerBookingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDinnerBookings::route('/'),
            // 'create' => CreateDinnerBooking::route('/create'),
            'edit' => EditDinnerBooking::route('/{record}/edit'),
        ];
    }
}
