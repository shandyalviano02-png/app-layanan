<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Models\ServiceRequest;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServiceRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('request_number'),
                TextEntry::make('serviceType.name')
                    ->label('Service type'),
                TextEntry::make('submitter.name')
                    ->label('Submitter')
                    ->placeholder('-'),
                TextEntry::make('applicant_name'),
                TextEntry::make('applicant_nik'),
                TextEntry::make('family_card_number'),
                TextEntry::make('address')
                    ->columnSpanFull(),
                TextEntry::make('village.name')
                    ->label('Village'),
                TextEntry::make('phone'),
                TextEntry::make('submitted_at')
                    ->dateTime(),
                TextEntry::make('officer.name')
                    ->label('Officer')
                    ->placeholder('-'),
                TextEntry::make('workUnit.name')
                    ->label('Work unit')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                IconEntry::make('is_priority')
                    ->boolean(),
                TextEntry::make('verification_result')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('officer_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('assessment_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('service_result')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('rejection_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('completed_at')
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
                    ->visible(fn (ServiceRequest $record): bool => $record->trashed()),
            ]);
    }
}
