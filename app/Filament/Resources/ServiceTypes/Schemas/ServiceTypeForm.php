<?php

namespace App\Filament\Resources\ServiceTypes\Schemas;

use App\Enums\ServiceHandler;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('category')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('handler')
                    ->options(ServiceHandler::class)
                    ->default('generic')
                    ->required(),
                Toggle::make('needs_assessment')
                    ->required(),
                TextInput::make('sla_days')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
                Repeater::make('requirements')
                    ->relationship('requirements')
                    ->columnSpanFull()
                    ->columns(4)
                    ->components([
                        TextInput::make('name')
                            ->label('Nama Persyaratan Dokumen')
                            ->required(),
                        Toggle::make('is_mandatory')
                            ->label('Wajib')
                            ->default(true),
                        TextInput::make('allowed_mimes')
                            ->label('Format File (Ekstensi)')
                            ->default('pdf,jpg,jpeg,png'),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(1),
                    ]),
            ]);
    }
}
