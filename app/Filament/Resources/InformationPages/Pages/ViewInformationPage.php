<?php

namespace App\Filament\Resources\InformationPages\Pages;

use App\Filament\Resources\InformationPages\InformationPageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInformationPage extends ViewRecord
{
    protected static string $resource = InformationPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
