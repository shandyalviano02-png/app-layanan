<?php

namespace App\Filament\Resources\PbiReactivations\Pages;

use App\Filament\Resources\PbiReactivations\PbiReactivationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPbiReactivation extends EditRecord
{
    protected static string $resource = PbiReactivationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
