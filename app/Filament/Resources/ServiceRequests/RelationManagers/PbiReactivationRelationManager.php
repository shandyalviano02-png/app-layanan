<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use App\Enums\MinistryDecision;
use App\Enums\PbiReason;
use App\Enums\ServiceRequestStatus;
use App\Models\PbiReactivation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PbiReactivationRelationManager extends RelationManager
{
    protected static string $relationship = 'pbiReactivation';

    protected static ?string $title = 'Detail Usulan Reaktivasi PBI-JK (Layanan 2)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->components([
                    TextInput::make('participant_name')
                        ->label('Nama Peserta BPJS')
                        ->required(),
                    TextInput::make('participant_nik')
                        ->label('NIK Peserta')
                        ->length(16)
                        ->required(),
                    TextInput::make('bpjs_card_number')
                        ->label('Nomor Kartu BPJS/KIS')
                        ->required(),
                ]),
                Grid::make(3)->components([
                    DatePicker::make('deactivated_date')
                        ->label('Tanggal Penonaktifan'),
                    Select::make('reason')
                        ->label('Alasan Reaktivasi')
                        ->options(collect(PbiReason::cases())->mapWithKeys(fn ($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))]))
                        ->required(),
                    TextInput::make('health_facility_name')
                        ->label('Fasilitas Kesehatan / RS'),
                ]),
                Grid::make(2)->components([
                    TextInput::make('health_letter_number')
                        ->label('Nomor Surat Keterangan Rawat/Medis'),
                    TextInput::make('decile')
                        ->label('Desil Kesejahteraan (1-10)')
                        ->numeric(),
                ]),
                Textarea::make('eligibility_notes')
                    ->label('Catatan Hasil Verifikasi Kelayakan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('participant_name')
            ->columns([
                TextColumn::make('participant_name')->label('Nama Peserta'),
                TextColumn::make('participant_nik')->label('NIK'),
                TextColumn::make('bpjs_card_number')->label('No. KIS'),
                TextColumn::make('reason')->label('Alasan')->badge(),
                TextColumn::make('decile')->label('Desil'),
                TextColumn::make('recommendation_number')->label('No. Rekomendasi')->placeholder('Belum terbit'),
                TextColumn::make('ministry_decision')->label('Keputusan Kemensos')->badge()->placeholder('-'),
                TextColumn::make('reactivated_date')->label('Tgl Aktif')->date('d M Y')->placeholder('-'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Input Data PBI-JK')
                    ->visible(fn () => $this->getOwnerRecord()->pbiReactivation === null),
            ])
            ->recordActions([
                Action::make('issue_recommendation')
                    ->label('Terbitkan Rekomendasi')
                    ->icon(Heroicon::OutlinedDocumentCheck)
                    ->color('primary')
                    ->visible(fn (PbiReactivation $record) => empty($record->recommendation_number))
                    ->form([
                        TextInput::make('recommendation_number')
                            ->label('Nomor Surat Rekomendasi')
                            ->default(fn () => '440/'.rand(100, 999).'/409.105/'.date('Y'))
                            ->required(),
                        Select::make('signer_id')
                            ->label('Penandatangan')
                            ->options(User::role(['pejabat_penandatangan', 'pimpinan'])->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (PbiReactivation $record, array $data) {
                        $record->update([
                            'recommendation_number' => $data['recommendation_number'],
                            'recommendation_issued_at' => now(),
                            'signer_id' => $data['signer_id'],
                        ]);
                        $this->getOwnerRecord()->update(['status' => ServiceRequestStatus::RecommendationIssued]);
                        Notification::make()->title('Rekomendasi Dinas Sosial berhasil diterbitkan')->success()->send();
                    }),
                Action::make('propose_to_ministry')
                    ->label('Input SIKS-NG Kemensos')
                    ->icon(Heroicon::OutlinedArrowUpOnSquare)
                    ->color('info')
                    ->visible(fn (PbiReactivation $record) => ! empty($record->recommendation_number) && empty($record->proposed_to_ministry_at))
                    ->requiresConfirmation()
                    ->action(function (PbiReactivation $record) {
                        $record->update(['proposed_to_ministry_at' => now()]);
                        $this->getOwnerRecord()->update(['status' => ServiceRequestStatus::ProposedToMinistry]);
                        Notification::make()->title('Usulan berhasil dicatat ke sistem SIKS-NG Kemensos')->info()->send();
                    }),
                Action::make('record_ministry_decision')
                    ->label('Keputusan Kemensos')
                    ->icon(Heroicon::OutlinedBuildingLibrary)
                    ->color('warning')
                    ->visible(fn (PbiReactivation $record) => ! empty($record->proposed_to_ministry_at) && empty($record->ministry_decision))
                    ->form([
                        Select::make('ministry_decision')
                            ->label('Keputusan')
                            ->options(collect(MinistryDecision::cases())->mapWithKeys(fn ($case) => [$case->value => ucfirst($case->value)]))
                            ->required(),
                        DateTimePicker::make('ministry_decided_at')
                            ->label('Tanggal Keputusan')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (PbiReactivation $record, array $data) {
                        $record->update([
                            'ministry_decision' => $data['ministry_decision'],
                            'ministry_decided_at' => $data['ministry_decided_at'],
                        ]);
                        $newStatus = ($data['ministry_decision'] === MinistryDecision::Approved->value)
                            ? ServiceRequestStatus::MinistryApproved
                            : ServiceRequestStatus::MinistryRejected;
                        $this->getOwnerRecord()->update(['status' => $newStatus]);
                        Notification::make()->title('Keputusan Kemensos berhasil dicatat')->success()->send();
                    }),
                Action::make('record_reactivated')
                    ->label('Tandai Aktif BPJS')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->visible(fn (PbiReactivation $record) => $record->ministry_decision?->value === 'approved' && empty($record->reactivated_date))
                    ->form([
                        DatePicker::make('reactivated_date')
                            ->label('Tanggal Aktif Kembali di BPJS Kesehatan')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (PbiReactivation $record, array $data) {
                        $record->update([
                            'reactivated_date' => $data['reactivated_date'],
                        ]);
                        $this->getOwnerRecord()->update([
                            'status' => ServiceRequestStatus::Reactivated,
                            'completed_at' => now(),
                        ]);
                        Notification::make()->title('Status kepesertaan PBI-JK berhasil diaktifkan kembali')->success()->send();
                    }),
                EditAction::make(),
            ]);
    }
}
