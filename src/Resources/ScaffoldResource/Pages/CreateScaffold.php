<?php

namespace FilamentAdmin\FilamentScaffold\Resources\ScaffoldResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\HtmlString;
use FilamentAdmin\FilamentScaffold\Models\Scaffold;
use FilamentAdmin\FilamentScaffold\Resources\ScaffoldResource;

class CreateScaffold extends CreateRecord
{
    protected static string $resource = ScaffoldResource::class;

    /********************************************
     * MAKE THE SAVE BUTTON ON 'PAGE' STICKY
     */
    public static bool $formActionsAreSticky = true;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        ScaffoldResource::generateFiles($data);

        return new Scaffold;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('create')
            ->label(__('filament-scaffold::filament-scaffold.action.create_scaffold'))
            ->requiresConfirmation()
            ->modalHeading(__('filament-scaffold::filament-scaffold.action.confirm_creation_heading'))
            ->modalDescription(new HtmlString(__('filament-scaffold::filament-scaffold.action.confirm_creation_description')))
            ->modalSubmitActionLabel(__('filament-scaffold::filament-scaffold.action.yes_confirm'))
            ->modalCancelActionLabel(__('filament-scaffold::filament-scaffold.action.no_cancel'))
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor('warning')
            ->action(fn () => $this->create())
            ->keyBindings(['mod+s']);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
