<?php

namespace App\Filament\Resources\Complaints\Schemas;

use App\Models\Complaint;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ComplaintInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('complaint_number'),
                TextEntry::make('complaint_category_id')
                    ->numeric(),
                TextEntry::make('reporter.name')
                    ->label('Reporter')
                    ->placeholder('-'),
                TextEntry::make('reporter_name'),
                TextEntry::make('reporter_phone'),
                TextEntry::make('location_detail')
                    ->columnSpanFull(),
                TextEntry::make('village.name')
                    ->label('Village'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('reported_at')
                    ->dateTime(),
                TextEntry::make('officer.name')
                    ->label('Officer')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('verification_result')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('action_taken')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('duplicateOf.id')
                    ->label('Duplicate of')
                    ->placeholder('-'),
                TextEntry::make('resolved_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Complaint $record): bool => $record->trashed()),
            ]);
    }
}
