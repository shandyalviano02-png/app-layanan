<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MonitoringRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'monitoringRecords';

    protected static ?string $title = 'Catatan Monitoring & Perkembangan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('monitoring_date')
                    ->label('Tanggal Monitoring')
                    ->default(now())
                    ->required(),
                Textarea::make('progress')
                    ->label('Perkembangan Kondisi Klien')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('result_notes')
                    ->label('Catatan & Rencana Tindak Lanjut')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('monitoring_date')
            ->columns([
                TextColumn::make('monitoring_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('officer.name')->label('Pekerja Sosial'),
                TextColumn::make('progress')->label('Perkembangan')->limit(60),
                TextColumn::make('created_at')->label('Dicatat')->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Monitoring')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['officer_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
