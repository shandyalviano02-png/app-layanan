<?php

namespace App\Filament\Resources\PbiReactivations\Schemas;

use App\Models\PbiReactivation;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PbiReactivationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('serviceRequest.id')
                    ->label('Service request'),
                TextEntry::make('participant_name'),
                TextEntry::make('participant_nik'),
                TextEntry::make('bpjs_card_number'),
                TextEntry::make('deactivated_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('reason')
                    ->badge(),
                TextEntry::make('health_facility_name')
                    ->placeholder('-'),
                TextEntry::make('health_letter_number')
                    ->placeholder('-'),
                TextEntry::make('decile')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('eligibility_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('recommendation_number')
                    ->placeholder('-'),
                TextEntry::make('recommendation_issued_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('signer.name')
                    ->label('Signer')
                    ->placeholder('-'),
                TextEntry::make('proposed_to_ministry_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('ministry_decision')
                    ->badge(),
                TextEntry::make('ministry_decided_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('reactivated_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (PbiReactivation $record): bool => $record->trashed()),
            ]);
    }
}
