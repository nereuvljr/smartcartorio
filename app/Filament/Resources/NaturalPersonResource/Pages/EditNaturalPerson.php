<?php

namespace App\Filament\Resources\NaturalPersonResource\Pages;

use App\Filament\Resources\NaturalPersonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNaturalPerson extends EditRecord
{
    protected static string $resource = NaturalPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
