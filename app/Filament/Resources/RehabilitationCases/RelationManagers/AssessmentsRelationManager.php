<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';

    protected static ?string $title = 'Riwayat Asesmen Klien';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->components([
                    DatePicker::make('assessment_date')
                        ->label('Tanggal Asesmen')
                        ->default(now())
                        ->required(),
                    Toggle::make('needs_referral')
                        ->label('Perlu Rujukan Lanjut (Panti / RS / Lembaga)')
                        ->default(false),
                ]),
                Textarea::make('result')
                    ->label('Hasil Asesmen Sosial (Kondisi Fisik, Mental, Sosial)')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('service_needs')
                    ->label('Kebutuhan Layanan / Intervensi')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('recommendation')
                    ->label('Rekomendasi Rencana Penanganan')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('assessment_date')
            ->columns([
                TextColumn::make('assessment_date')
                    ->label('Tanggal Asesmen')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Pekerja Sosial')
                    ->placeholder('-'),
                TextColumn::make('result')
                    ->label('Ringkasan Hasil')
                    ->limit(50),
                IconColumn::make('needs_referral')
                    ->label('Butuh Rujukan')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dicatat')
                    ->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Asesmen')
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
