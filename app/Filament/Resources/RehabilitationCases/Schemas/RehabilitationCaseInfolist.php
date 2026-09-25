<?php

namespace App\Filament\Resources\RehabilitationCases\Schemas;

use App\Models\RehabilitationCase;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RehabilitationCaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('case_number'),
                TextEntry::make('client.name')
                    ->label('Client'),
                TextEntry::make('serviceRequest.id')
                    ->label('Service request')
                    ->placeholder('-'),
                TextEntry::make('complaint.id')
                    ->label('Complaint')
                    ->placeholder('-'),
                TextEntry::make('officer.name')
                    ->label('Officer'),
                TextEntry::make('handling_type')
                    ->badge(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('handling_result')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('received_at')
                    ->dateTime(),
                TextEntry::make('closed_at')
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
                    ->visible(fn (RehabilitationCase $record): bool => $record->trashed()),
            ]);
    }
}
