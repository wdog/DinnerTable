<?php

namespace App\Filament\App\Pages;

use BackedEnum;
use Filament\Pages\Page;
use App\Models\AppReview;
use Filament\Schemas\Schema;
use App\Forms\Components\RatingStar;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;

/**
 * Pagina per lasciare una recensione dell'applicazione.
 *
 * Ogni utente può lasciare una sola recensione con voto (0-5) e commento opzionale.
 */
class LeaveReview extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'tabler-star';

    protected static ?string $title = 'Lascia una Recensione';

    protected static ?string $navigationLabel = 'Recensione';

    // Nascondo dalla sidebar, sarà visibile solo nel menu utente
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    protected string $view = 'filament.app.pages.leave-review';

    public function mount(): void
    {
        // Carica recensione esistente se presente
        $review = Auth::user()->appReview;

        if ($review) {
            $this->form->fill([
                'rating'  => $review->rating,
                'comment' => $review->comment,
            ]);
        } else {
            $this->form->fill();
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Valuta la tua esperienza')
                    ->schema([
                        RatingStar::make('rating')
                            ->label('Quanto ti piace DinnerTable?')
                            ->required()
                            ->maxStars(5),

                        Textarea::make('comment')
                            ->label('Il tuo commento (opzionale)')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpan(2)
                            ->placeholder('Raccontaci cosa ti piace o cosa potremmo migliorare...'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        AppReview::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'rating'  => $data['rating'],
                'comment' => $data['comment'],
            ]
        );

        Notification::make()
            ->title('Grazie per la tua recensione!')
            ->success()
            ->send();
    }
}
