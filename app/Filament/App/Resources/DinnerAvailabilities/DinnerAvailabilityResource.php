<?php

namespace App\Filament\App\Resources\DinnerAvailabilities;

use App\Filament\App\Resources\DinnerAvailabilities\Pages\CreateDinnerAvailability;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\EditDinnerAvailability;
use App\Filament\App\Resources\DinnerAvailabilities\Pages\ListDinnerAvailabilities;
use App\Filament\App\Resources\DinnerAvailabilities\Schemas\DinnerAvailabilityForm;
use App\Filament\App\Resources\DinnerAvailabilities\Tables\DinnerAvailabilitiesTable;
use App\Models\DinnerAvailability;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DinnerAvailabilityResource extends Resource
{
    protected static ?string $model = DinnerAvailability::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Dinner Date';

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
            'index' => ListDinnerAvailabilities::route('/'),
            'create' => CreateDinnerAvailability::route('/create'),
            'edit' => EditDinnerAvailability::route('/{record}/edit'),
        ];
    }
}
