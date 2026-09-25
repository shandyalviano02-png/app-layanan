<?php

namespace App\Filament\Resources\RehabilitationCases\Tables;

use App\Enums\HandlingType;
use App\Enums\RehabilitationCaseStatus;
use App\Models\RehabilitationCase;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RehabilitationCasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')
                    ->label('No. Kasus')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('client.name')
                    ->label('Nama Klien (PPKS)')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.nik')
                    ->label('NIK Klien')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('client.clientCategory.name')
                    ->label('Kategori PPKS')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('officer.name')
                    ->label('Pekerja Sosial')
                    ->placeholder('Belum ditugaskan')
                    ->searchable(),
                TextColumn::make('handling_type')
                    ->label('Jenis Penanganan')
                    ->badge()
                    ->color('secondary')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status Kasus')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof RehabilitationCaseStatus ? $state->label() : ($state ? RehabilitationCaseStatus::tryFrom($state)?->label() ?? $state : '-'))
                    ->color(fn ($state) => match ($state instanceof RehabilitationCaseStatus ? $state->value : (string) $state) {
                        'new' => 'gray',
                        'assessment', 'referral', 'monitoring' => 'info',
                        'in_handling' => 'primary',
                        'closed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('received_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->label('Tanggal Ditutup')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->defaultSort('received_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kasus')
                    ->options(collect(RehabilitationCaseStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                SelectFilter::make('handling_type')
                    ->label('Jenis Penanganan')
                    ->options(collect(HandlingType::cases())->mapWithKeys(fn ($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))])),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('close_case')
                    ->label('Tutup Kasus')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (RehabilitationCase $record) => $record->status?->value !== 'closed')
                    ->form([
                        Textarea::make('handling_result')
                            ->label('Hasil Akhir Penanganan / Terminasi Kasus')
                            ->required(),
                    ])
                    ->action(function (RehabilitationCase $record, array $data) {
                        $record->update([
                            'status' => RehabilitationCaseStatus::Closed,
                            'handling_result' => $data['handling_result'],
                            'closed_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Kasus rehabilitasi sosial berhasil diselesaikan dan ditutup')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
