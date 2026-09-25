<?php

namespace App\Filament\Resources\Complaints\Tables;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ComplaintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('complaint_number')
                    ->label('No. Pengaduan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reporter_name')
                    ->label('Nama Pelapor')
                    ->searchable(),
                TextColumn::make('reporter_phone')
                    ->label('No. HP Pelapor')
                    ->searchable(),
                TextColumn::make('village.name')
                    ->label('Desa / Kelurahan')
                    ->searchable(),
                TextColumn::make('reported_at')
                    ->label('Tgl Laporan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Petugas Ditugaskan')
                    ->searchable()
                    ->placeholder('Belum ditugaskan'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ComplaintStatus ? $state->label() : ($state ? ComplaintStatus::tryFrom((string) $state)?->label() ?? $state : '-'))
                    ->color(fn ($state) => match ($state instanceof ComplaintStatus ? $state->value : (string) $state) {
                        'received' => 'gray',
                        'verification' => 'warning',
                        'clarification_requested' => 'warning',
                        'dispatched' => 'info',
                        'in_handling' => 'primary',
                        'resolved' => 'success',
                        'duplicate' => 'gray',
                        'invalid' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('resolved_at')
                    ->label('Tgl Selesai')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pengaduan')
                    ->options(collect(ComplaintStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                SelectFilter::make('complaint_category_id')
                    ->label('Kategori Pengaduan')
                    ->relationship('category', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // 1. Verify Action
                Action::make('verify')
                    ->label('Verifikasi Laporan')
                    ->icon(Heroicon::OutlinedMagnifyingGlass)
                    ->color('warning')
                    ->visible(fn (Complaint $record) => in_array($record->status, [ComplaintStatus::Received, ComplaintStatus::ClarificationRequested]))
                    ->form([
                        Textarea::make('verification_result')
                            ->label('Catatan Hasil Verifikasi')
                            ->required(),
                    ])
                    ->action(function (Complaint $record, array $data) {
                        $record->update([
                            'status' => ComplaintStatus::Verification,
                            'verification_result' => $data['verification_result'],
                        ]);
                        Notification::make()->title('Laporan dalam status Verifikasi')->info()->send();
                    }),

                // 2. Request Clarification Action
                Action::make('request_clarification')
                    ->label('Minta Klarifikasi')
                    ->icon(Heroicon::OutlinedQuestionMarkCircle)
                    ->color('warning')
                    ->visible(fn (Complaint $record) => in_array($record->status, [ComplaintStatus::Received, ComplaintStatus::Verification]))
                    ->form([
                        Textarea::make('clarification_notes')
                            ->label('Poin yang Perlu Diklarifikasi oleh Pelapor')
                            ->required(),
                    ])
                    ->action(function (Complaint $record, array $data) {
                        $record->update([
                            'status' => ComplaintStatus::ClarificationRequested,
                            'verification_result' => ($record->verification_result ? $record->verification_result."\n\n" : '').'Klarifikasi yang diminta: '.$data['clarification_notes'],
                        ]);
                        Notification::make()->title('Permintaan klarifikasi dicatat')->warning()->send();
                    }),

                // 3. Dispatch Action
                Action::make('dispatch')
                    ->label('Disposisikan')
                    ->icon(Heroicon::OutlinedUserPlus)
                    ->color('info')
                    ->visible(fn (Complaint $record) => in_array($record->status, [ComplaintStatus::Received, ComplaintStatus::Verification]))
                    ->form([
                        Select::make('officer_id')
                            ->label('Petugas yang Ditugaskan')
                            ->options(User::role(['petugas_dinsos', 'administrator'])->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan Instruksi Disposisi'),
                    ])
                    ->action(function (Complaint $record, array $data) {
                        $record->update([
                            'status' => ComplaintStatus::Dispatched,
                            'officer_id' => $data['officer_id'],
                        ]);
                        Notification::make()->title('Pengaduan berhasil didisposisikan')->success()->send();
                    }),

                // 4. Record Handling Action
                Action::make('record_handling')
                    ->label('Proses Penanganan')
                    ->icon(Heroicon::OutlinedWrenchScrewdriver)
                    ->color('primary')
                    ->visible(fn (Complaint $record) => in_array($record->status, [ComplaintStatus::Dispatched, ComplaintStatus::InHandling]))
                    ->form([
                        Textarea::make('action_taken')
                            ->label('Tindakan / Langkah Penanganan yang Dilakukan')
                            ->required(),
                    ])
                    ->action(function (Complaint $record, array $data) {
                        $record->update([
                            'status' => ComplaintStatus::InHandling,
                            'action_taken' => $data['action_taken'],
                        ]);
                        Notification::make()->title('Progres penanganan diperbarui')->success()->send();
                    }),

                // 5. Resolve Action
                Action::make('resolve')
                    ->label('Selesaikan Laporan')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Complaint $record) => in_array($record->status, [ComplaintStatus::InHandling, ComplaintStatus::Dispatched]))
                    ->form([
                        Textarea::make('action_taken')
                            ->label('Tindakan Akhir / Hasil Penyelesaian')
                            ->default(fn (Complaint $record) => $record->action_taken)
                            ->required(),
                    ])
                    ->action(function (Complaint $record, array $data) {
                        $record->update([
                            'status' => ComplaintStatus::Resolved,
                            'action_taken' => $data['action_taken'],
                            'resolved_at' => now(),
                        ]);
                        Notification::make()->title('Pengaduan berhasil diselesaikan')->success()->send();
                    }),

                // 6. Mark Duplicate Action
                Action::make('mark_duplicate')
                    ->label('Tandai Duplikat')
                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                    ->color('gray')
                    ->visible(fn (Complaint $record) => ! $record->status->isClosed())
                    ->form([
                        Select::make('duplicate_of_id')
                            ->label('Laporan Induk (Nomor Pengaduan)')
                            ->options(fn (Complaint $record) => Complaint::where('id', '!=', $record->id)->pluck('complaint_number', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Complaint $record, array $data) {
                        $record->update([
                            'status' => ComplaintStatus::Duplicate,
                            'duplicate_of_id' => $data['duplicate_of_id'],
                            'resolved_at' => now(),
                        ]);
                        Notification::make()->title('Pengaduan ditandai sebagai duplikat')->info()->send();
                    }),
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
