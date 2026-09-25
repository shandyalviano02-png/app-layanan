<?php

namespace App\Filament\Resources\PbiReactivations\Pages;

use App\Filament\Resources\PbiReactivations\PbiReactivationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPbiReactivations extends ListRecords
{
    protected static string $resource = PbiReactivationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
