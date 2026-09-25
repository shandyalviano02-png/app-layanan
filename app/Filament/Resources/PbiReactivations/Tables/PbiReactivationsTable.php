<?php

namespace App\Filament\Resources\PbiReactivations\Tables;

use App\Enums\MinistryDecision;
use App\Enums\PbiReason;
use App\Models\PbiReactivation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PbiReactivationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serviceRequest.request_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('participant_name')
                    ->label('Nama Peserta KIS')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('participant_nik')
                    ->label('NIK Peserta')
                    ->searchable(),
                TextColumn::make('bpjs_card_number')
                    ->label('No. BPJS/KIS')
                    ->searchable(),
                IconColumn::make('serviceRequest.is_priority')
                    ->label('Darurat RS')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedExclamationTriangle)
                    ->trueColor('danger')
                    ->falseIcon(Heroicon::OutlinedMinus)
                    ->falseColor('gray'),
                TextColumn::make('health_facility_name')
                    ->label('Faskes / RS Perujuk')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('reason')
                    ->label('Alasan Penonaktifan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof PbiReason ? $state->label() : ($state ? PbiReason::tryFrom((string) $state)?->label() ?? $state : '-'))
                    ->color('info'),
                TextColumn::make('recommendation_number')
                    ->label('No. Rekomendasi')
                    ->searchable()
                    ->placeholder('Belum Terbit'),
                TextColumn::make('ministry_decision')
                    ->label('Status Kemensos')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof MinistryDecision ? $state->label() : ($state ? MinistryDecision::tryFrom((string) $state)?->label() ?? $state : '-'))
                    ->color(fn ($state) => match ($state instanceof MinistryDecision ? $state->value : (string) $state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('reactivated_date')
                    ->label('Tgl KIS Aktif')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('ministry_decision')
                    ->label('Status Keputusan Kemensos')
                    ->options(collect(MinistryDecision::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                SelectFilter::make('reason')
                    ->label('Alasan Penonaktifan')
                    ->options(collect(PbiReason::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                TernaryFilter::make('is_priority')
                    ->label('Prioritas Darurat Medis')
                    ->queries(
                        true: fn ($query) => $query->whereHas('serviceRequest', fn ($sq) => $sq->where('is_priority', true)),
                        false: fn ($query) => $query->whereHas('serviceRequest', fn ($sq) => $sq->where('is_priority', false)),
                    ),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // 1. Issue Recommendation
                Action::make('issue_recommendation')
                    ->label('Terbitkan Rekomendasi')
                    ->icon(Heroicon::OutlinedDocumentCheck)
                    ->color('primary')
                    ->visible(fn (PbiReactivation $record) => empty($record->recommendation_number))
                    ->form([
                        TextInput::make('recommendation_number')
                            ->label('Nomor Surat Rekomendasi Dinsos')
                            ->default(fn () => '400.9/'.rand(100, 999).'/REK-PBI/'.date('Y'))
                            ->required(),
                        Select::make('signer_id')
                            ->label('Pejabat Penandatangan')
                            ->options(User::role(['pejabat_penandatangan', 'pimpinan'])->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (PbiReactivation $record, array $data) {
                        $record->update([
                            'recommendation_number' => $data['recommendation_number'],
                            'signer_id' => $data['signer_id'],
                            'recommendation_issued_at' => now(),
                        ]);
                        Notification::make()->title('Surat Rekomendasi Reaktivasi diterbitkan')->success()->send();
                    }),

                // 2. Propose to Ministry
                Action::make('propose_to_ministry')
                    ->label('Usulkan ke Kemensos')
                    ->icon(Heroicon::OutlinedArrowUpOnSquare)
                    ->color('warning')
                    ->visible(fn (PbiReactivation $record) => ! empty($record->recommendation_number) && empty($record->proposed_to_ministry_at))
                    ->requiresConfirmation()
                    ->action(function (PbiReactivation $record) {
                        $record->update([
                            'proposed_to_ministry_at' => now(),
                        ]);
                        Notification::make()->title('Usulan berhasil dicatat ke Kemensos via SIKS-NG')->success()->send();
                    }),

                // 3. Record Ministry Decision
                Action::make('record_ministry_decision')
                    ->label('Catat Putusan Kemensos')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('info')
                    ->visible(fn (PbiReactivation $record) => ! empty($record->proposed_to_ministry_at) && $record->ministry_decision === MinistryDecision::Pending)
                    ->form([
                        Select::make('ministry_decision')
                            ->label('Keputusan Kementerian Sosial')
                            ->options(collect(MinistryDecision::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required(),
                    ])
                    ->action(function (PbiReactivation $record, array $data) {
                        $record->update([
                            'ministry_decision' => $data['ministry_decision'],
                            'ministry_decided_at' => now(),
                        ]);
                        Notification::make()->title('Keputusan Kemensos berhasil disimpan')->success()->send();
                    }),

                // 4. Record Reactivated
                Action::make('record_reactivated')
                    ->label('Catat KIS Aktif')
                    ->icon(Heroicon::OutlinedHeart)
                    ->color('success')
                    ->visible(fn (PbiReactivation $record) => $record->ministry_decision === MinistryDecision::Approved && empty($record->reactivated_date))
                    ->form([
                        DatePicker::make('reactivated_date')
                            ->label('Tanggal KIS Resmi Aktif')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (PbiReactivation $record, array $data) {
                        $record->update([
                            'reactivated_date' => $data['reactivated_date'],
                        ]);
                        Notification::make()->title('Kepesertaan KIS/PBI telah aktif kembali')->success()->send();
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
