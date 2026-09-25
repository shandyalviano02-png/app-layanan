<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use App\Enums\ServiceRequestStatus;
use App\Models\DtsenCertificate;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DtsenCertificateRelationManager extends RelationManager
{
    protected static string $relationship = 'dtsenCertificate';

    protected static ?string $title = 'Detail SK Terdaftar DTSEN (Layanan 1)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->components([
                    Select::make('dtsen_purpose_id')
                        ->label('Tujuan Penggunaan SK')
                        ->relationship('dtsenPurpose', 'name')
                        ->required(),
                    TextInput::make('purpose_description')
                        ->label('Keterangan Tambahan Tujuan'),
                ]),
                Grid::make(3)->components([
                    TextInput::make('subject_name')
                        ->label('Nama Yang Dituju / Bersangkutan')
                        ->required(),
                    TextInput::make('subject_nik')
                        ->label('NIK Yang Bersangkutan')
                        ->length(16)
                        ->required(),
                    TextInput::make('relationship_to_applicant')
                        ->label('Hubungan dengan Pemohon')
                        ->placeholder('Contoh: Diri Sendiri / Anak Kandung')
                        ->required(),
                ]),
                Grid::make(3)->components([
                    Toggle::make('is_registered')
                        ->label('Terdaftar di DTKS/DTSEN')
                        ->default(true),
                    TextInput::make('decile')
                        ->label('Desil Kesejahteraan (1-10)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10),
                    DateTimePicker::make('checked_at')
                        ->label('Waktu Pengecekan SIKS-NG')
                        ->default(now()),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject_name')
            ->columns([
                TextColumn::make('subject_name')->label('Nama Subjek'),
                TextColumn::make('subject_nik')->label('NIK Subjek'),
                TextColumn::make('dtsenPurpose.name')->label('Tujuan SK'),
                IconColumn::make('is_registered')->label('Terdaftar')->boolean(),
                TextColumn::make('decile')->label('Desil')->badge(),
                TextColumn::make('certificate_number')->label('Nomor SK')->placeholder('Belum terbit')->copyable(),
                TextColumn::make('issued_at')->label('Tanggal Terbit')->dateTime('d M Y H:i')->placeholder('-'),
                TextColumn::make('verification_code')->label('Kode Verifikasi QR')->placeholder('-'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Input Data SK DTSEN')
                    ->visible(fn () => $this->getOwnerRecord()->dtsenCertificate === null),
            ])
            ->recordActions([
                Action::make('check_siks_ng')
                    ->label('Catat SIKS-NG')
                    ->icon(Heroicon::OutlinedMagnifyingGlass)
                    ->color('info')
                    ->form([
                        Toggle::make('is_registered')->label('Terdaftar dalam DTKS / DTSEN')->required()->default(true),
                        TextInput::make('decile')->label('Desil Kesejahteraan (1-10)')->numeric()->required(),
                        DateTimePicker::make('checked_at')->label('Waktu Cek SIKS-NG')->default(now())->required(),
                    ])
                    ->action(function (DtsenCertificate $record, array $data) {
                        $record->update([
                            'is_registered' => $data['is_registered'],
                            'decile' => $data['decile'],
                            'checked_at' => $data['checked_at'],
                            'checker_id' => auth()->id(),
                        ]);
                        $this->getOwnerRecord()->update(['status' => ServiceRequestStatus::DataVerification]);
                        Notification::make()->title('Data hasil cek SIKS-NG berhasil disimpan')->success()->send();
                    }),
                Action::make('issue_certificate')
                    ->label('Terbitkan SK')
                    ->icon(Heroicon::OutlinedDocumentCheck)
                    ->color('success')
                    ->visible(fn (DtsenCertificate $record) => empty($record->certificate_number))
                    ->form([
                        TextInput::make('certificate_number')
                            ->label('Nomor SK DTSEN')
                            ->default(fn () => '400.9/'.rand(100, 999).'/409.105/'.date('Y'))
                            ->required(),
                        DatePicker::make('valid_until')
                            ->label('Masa Berlaku Sampai')
                            ->default(now()->addMonths(1))
                            ->required(),
                        Select::make('signer_id')
                            ->label('Pejabat Penandatangan')
                            ->options(User::role(['pejabat_penandatangan', 'pimpinan'])->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (DtsenCertificate $record, array $data) {
                        // Business logic validation: Decile check
                        if ($record->dtsenPurpose && $record->decile !== null && $record->decile > $record->dtsenPurpose->max_decile) {
                            Notification::make()
                                ->title('Desil di luar ketentuan')
                                ->body('Desil pemohon ('.$record->decile.') melebihi desil maksimal ('.$record->dtsenPurpose->max_decile.') untuk tujuan '.$record->dtsenPurpose->name)
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update([
                            'certificate_number' => $data['certificate_number'],
                            'issued_at' => now(),
                            'valid_until' => $data['valid_until'],
                            'signer_id' => $data['signer_id'],
                            'verification_code' => 'DTSEN-V-'.strtoupper(substr(md5(uniqid()), 0, 10)),
                        ]);

                        $this->getOwnerRecord()->update([
                            'status' => ServiceRequestStatus::Issued,
                        ]);

                        Notification::make()
                            ->title('Surat Keterangan Terdaftar DTSEN berhasil diterbitkan!')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ]);
    }
}
