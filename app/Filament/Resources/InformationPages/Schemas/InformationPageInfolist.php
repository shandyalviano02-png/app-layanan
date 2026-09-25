<?php

namespace App\Filament\Resources\InformationPages\Schemas;

use App\Models\InformationPage;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InformationPageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('category')
                    ->badge(),
                TextEntry::make('serviceType.name')
                    ->label('Service type')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('requirements')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('procedure')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('service_hours')
                    ->placeholder('-'),
                TextEntry::make('location')
                    ->placeholder('-'),
                TextEntry::make('contact')
                    ->placeholder('-'),
                TextEntry::make('publish_status')
                    ->badge(),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('manager.name')
                    ->label('Manager'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (InformationPage $record): bool => $record->trashed()),
            ]);
    }
}
