<?php

namespace App\Filament\Resources\NaturalPersonResource\Pages;

use App\Filament\Resources\NaturalPersonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNaturalPerson extends CreateRecord
{
    protected static string $resource = NaturalPersonResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
