<?php

namespace App\Filament\Admin\Resources\DinnerGroups;

use App\Filament\Admin\Resources\DinnerGroups\Pages\CreateDinnerGroup;
use App\Filament\Admin\Resources\DinnerGroups\Pages\EditDinnerGroup;
use App\Filament\Admin\Resources\DinnerGroups\Pages\ListDinnerGroups;
use App\Filament\Admin\Resources\DinnerGroups\Schemas\DinnerGroupForm;
use App\Filament\Admin\Resources\DinnerGroups\Tables\DinnerGroupsTable;
use App\Models\DinnerGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DinnerGroupResource extends Resource
{
    protected static ?string $model = DinnerGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Gruppi Cena';

    protected static ?string $modelLabel = 'gruppo cena';

    protected static ?string $pluralModelLabel = 'gruppi cena';

    public static function form(Schema $schema): Schema
    {
        return DinnerGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DinnerGroupsTable::configure($table);
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
            'index' => ListDinnerGroups::route('/'),
            'create' => CreateDinnerGroup::route('/create'),
            'edit' => EditDinnerGroup::route('/{record}/edit'),
        ];
    }
}
