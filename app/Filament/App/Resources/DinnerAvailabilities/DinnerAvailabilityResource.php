<?php

namespace App\Filament\App\Resources\DinnerAvailabilities;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Models\DinnerAvailability;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\EditDinnerAvailability;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\CreateDinnerAvailability;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\ListDinnerAvailabilities;
use App\Filament\App\Resources\DinnerAvailabilities\Schemas\DinnerAvailabilityForm;
use App\Filament\App\Resources\DinnerAvailabilities\Tables\DinnerAvailabilitiesTable;

class DinnerAvailabilityResource extends Resource
{
    protected static ?string $model = DinnerAvailability::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Dinner Date';

    protected static ?string $navigationLabel = 'Disponibilità';

    /**
     * Gruppo di navigazione.
     */
    protected static string|UnitEnum|null $navigationGroup = 'Gestione Cene';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('dinnerDate', fn ($q) => $q->where('dinner_group_id', Auth::user()->dinner_group_id));
    }

    public static function form(Schema $schema): Schema
    {
        return DinnerAvailabilityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DinnerAvailabilitiesTable::configure($table);
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
            'index'  => ListDinnerAvailabilities::route('/'),
            'create' => CreateDinnerAvailability::route('/create'),
            'edit'   => EditDinnerAvailability::route('/{record}/edit'),
        ];
    }
}
