<?php

namespace App\Filament\App\Pages;

use UnitEnum;
use BackedEnum;
use Filament\Pages\Page;

class TutorialPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Guida';

    protected static ?string $title = 'Guida all\'uso di DinnerTable';

    protected string $view = 'filament.app.pages.tutorial-page';

    protected static ?int $navigationSort = 99;

    protected static string|UnitEnum|null $navigationGroup = 'Gestione Cene';

    public static function canAccess(): bool
    {
        return true;
    }
}
