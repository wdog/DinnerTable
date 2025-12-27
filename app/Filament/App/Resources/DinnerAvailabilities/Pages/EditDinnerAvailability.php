<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Pages;

use Filament\Actions\DeleteAction;
use App\Enums\DinnerAvailabilityStatus;
use Filament\Resources\Pages\EditRecord;
use App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource;

/**
 * Pagina di modifica disponibilità con gestione stato read-only.
 *
 * Disponibilità COMPLETED o nel passato sono in sola lettura:
 * - Form disabilitato
 * - Nessuna azione header
 * - Relation manager in sola lettura
 */
class EditDinnerAvailability extends EditRecord
{
    protected static string $resource = DinnerAvailabilityResource::class;

    /**
     * Carica sempre la relazione dinnerDate per verificare lo stato read-only.
     */
    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return parent::resolveRecord($key)->load('dinnerDate');
    }

    /**
     * Azioni header disponibili.
     *
     * Disponibilità completate o passate non hanno azioni
     * (nessun pulsante elimina o altro).
     */
    protected function getHeaderActions(): array
    {
        // Se completata o passata, nessuna azione
        if ($this->isReadOnly()) {
            return [];
        }

        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Azioni del form.
     *
     * Se la disponibilità è read-only, nasconde i pulsanti Salva/Annulla.
     */
    protected function getFormActions(): array
    {
        if ($this->isReadOnly()) {
            return [];
        }

        return parent::getFormActions();
    }

    /**
     * Verifica se la disponibilità è in sola lettura.
     *
     * Condizioni per read-only:
     * - Stato COMPLETED
     * - Data nel passato
     */
    protected function isReadOnly(): bool
    {
        if ( ! $this->record) {
            return false;
        }

        // Completata = read-only
        if ($this->record->status === DinnerAvailabilityStatus::COMPLETED) {
            return true;
        }

        // Data passata = read-only
        if ($this->record->dinnerDate && $this->record->dinnerDate->dinner_date < today()) {
            return true;
        }

        return false;
    }

    /**
     * Monta la pagina e disabilita il form se necessario.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Il form sarà disabilitato tramite il metodo form() nel resource
        return parent::mutateFormDataBeforeFill($data);
    }
}
