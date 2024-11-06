<?php

namespace App\Filament\Resources\NaturalPersonResource\Pages;

use App\Filament\Resources\NaturalPersonResource;
use Filament\Actions;  // Adicione esta linha
use Filament\Resources\Pages\ViewRecord;

class ViewNaturalPerson extends ViewRecord
{
    protected static string $resource = NaturalPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
