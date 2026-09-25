<?php

namespace App\Filament\Resources\DtsenPurposes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DtsenPurposeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('max_decile')
                    ->required()
                    ->numeric(),
                TextInput::make('validity_days')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
