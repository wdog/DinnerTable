<?php

namespace App\Filament\App\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\AppReview;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

/**
 * Pagina per lasciare una recensione dell'applicazione.
 *
 * Ogni utente può lasciare una sola recensione con voto (0-5) e commento opzionale.
 */
class LeaveReview extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.app.pages.leave-review';

    protected static ?string $navigationIcon = 'tabler-star';

    protected static ?string $title = 'Lascia una Recensione';

    protected static ?string $navigationLabel = 'Recensione';

    // Nascondo dalla sidebar, sarà visibile solo nel menu utente
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('rating')
                    ->label('Quanto ti piace DinnerTable?')
                    ->required()
                    ->options([
                        0 => '0 - Per niente',
                        1 => '1 - Scarso',
                        2 => '2 - Sufficiente',
                        3 => '3 - Buono',
                        4 => '4 - Ottimo',
                        5 => '5 - Eccellente',
                    ])
                    ->inline()
                    ->columnSpanFull(),

                Textarea::make('comment')
                    ->label('Il tuo commento (opzionale)')
                    ->rows(4)
                    ->maxLength(1000)
                    ->placeholder('Raccontaci cosa ti piace o cosa potremmo migliorare...')
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
