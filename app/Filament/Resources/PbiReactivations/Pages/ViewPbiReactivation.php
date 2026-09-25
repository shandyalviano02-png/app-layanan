<?php

namespace App\Filament\Resources\PbiReactivations\Pages;

use App\Filament\Resources\PbiReactivations\PbiReactivationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPbiReactivation extends ViewRecord
{
    protected static string $resource = PbiReactivationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
