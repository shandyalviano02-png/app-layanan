<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('serviceType.name')
                    ->label('Layanan')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('applicant_name')
                    ->label('Nama Pemohon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('applicant_nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('village.district.name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('village.name')
                    ->label('Desa/Kelurahan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ServiceRequestStatus ? $state->label() : ($state ? ServiceRequestStatus::tryFrom($state)?->label() ?? $state : '-'))
                    ->color(fn ($state) => match ($state instanceof ServiceRequestStatus ? $state->value : (string) $state) {
                        'submitted' => 'gray',
                        'document_check', 'data_verification', 'eligibility_verification', 'verification', 'assessment' => 'info',
                        'revision_requested' => 'warning',
                        'in_process' => 'primary',
                        'awaiting_approval' => 'warning',
                        'issued', 'recommendation_issued', 'ministry_approved', 'reactivated', 'completed' => 'success',
                        'rejected', 'ministry_rejected' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                IconColumn::make('is_priority')
                    ->label('Prioritas')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedExclamationTriangle)
                    ->falseIcon(Heroicon::OutlinedMinus)
                    ->trueColor('danger')
                    ->falseColor('gray')
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->placeholder('Belum ditugaskan')
                    ->searchable(),
                TextColumn::make('submitted_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label('Tanggal Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Permohonan')
                    ->options(collect(ServiceRequestStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                SelectFilter::make('service_type_id')
                    ->label('Jenis Layanan')
                    ->relationship('serviceType', 'name'),
                TernaryFilter::make('is_priority')
                    ->label('Prioritas Khusus'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('verify_documents')
                    ->label('Periksa Berkas')
                    ->icon(Heroicon::OutlinedDocumentCheck)
                    ->color('info')
                    ->visible(fn (ServiceRequest $record) => in_array($record->status?->value ?? (string) $record->status, ['submitted', 'revision_requested']))
                    ->requiresConfirmation()
                    ->action(function (ServiceRequest $record): void {
                        $record->update(['status' => ServiceRequestStatus::DocumentCheck]);
                        Notification::make()
                            ->title('Status berkas diubah ke Pemeriksaan Berkas')
                            ->success()
                            ->send();
                    }),
                Action::make('request_revision')
                    ->label('Minta Revisi')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('warning')
                    ->visible(fn (ServiceRequest $record) => in_array($record->status?->value ?? (string) $record->status, ['submitted', 'document_check', 'data_verification']))
                    ->form([
                        Textarea::make('officer_notes')
                            ->label('Catatan Perbaikan / Kekurangan Berkas')
                            ->required(),
                    ])
                    ->action(function (ServiceRequest $record, array $data): void {
                        $record->update([
                            'status' => ServiceRequestStatus::RevisionRequested,
                            'officer_notes' => $data['officer_notes'],
                        ]);
                        Notification::make()
                            ->title('Permintaan perbaikan berkas telah dicatat')
                            ->warning()
                            ->send();
                    }),
                Action::make('assign_officer')
                    ->label('Tugaskan Petugas')
                    ->icon(Heroicon::OutlinedUserPlus)
                    ->color('primary')
                    ->visible(fn (ServiceRequest $record) => auth()->user()?->hasAnyRole(['administrator', 'petugas_dinsos']) && ! $record->officer_id)
                    ->form([
                        Select::make('officer_id')
                            ->label('Pilih Petugas')
                            ->options(User::role(['petugas_dinsos', 'administrator'])->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (ServiceRequest $record, array $data): void {
                        $record->update([
                            'officer_id' => $data['officer_id'],
                            'status' => ServiceRequestStatus::InProcess,
                        ]);
                        Notification::make()
                            ->title('Petugas berhasil ditugaskan')
                            ->success()
                            ->send();
                    }),
                Action::make('complete')
                    ->label('Selesaikan')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (ServiceRequest $record) => ! in_array($record->status?->value ?? (string) $record->status, ['completed', 'rejected', 'ministry_rejected']))
                    ->form([
                        Textarea::make('service_result')
                            ->label('Hasil Pelayanan')
                            ->required(),
                    ])
                    ->action(function (ServiceRequest $record, array $data): void {
                        $record->update([
                            'status' => ServiceRequestStatus::Completed,
                            'service_result' => $data['service_result'],
                            'completed_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Permohonan layanan dinyatakan Selesai')
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
