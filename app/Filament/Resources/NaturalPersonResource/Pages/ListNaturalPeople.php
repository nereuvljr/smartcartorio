<?php

namespace App\Filament\Resources\NaturalPersonResource\Pages;

use App\Filament\Resources\NaturalPersonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNaturalPeople extends ListRecords
{
    protected static string $resource = NaturalPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
