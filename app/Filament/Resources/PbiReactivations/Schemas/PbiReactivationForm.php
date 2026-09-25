<?php

namespace App\Filament\Resources\PbiReactivations\Schemas;

use App\Enums\MinistryDecision;
use App\Enums\PbiReason;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PbiReactivationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kaitan Permohonan & Identitas Peserta')
                    ->description('Data tiket permohonan dan identitas peserta KIS')
                    ->schema([
                        Select::make('service_request_id')
                            ->label('Tiket Permohonan Layanan')
                            ->relationship('serviceRequest', 'request_number')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Grid::make(3)->schema([
                            TextInput::make('participant_name')
                                ->label('Nama Peserta KIS')
                                ->required(),
                            TextInput::make('participant_nik')
                                ->label('NIK Peserta (16 Digit)')
                                ->length(16)
                                ->required(),
                            TextInput::make('bpjs_card_number')
                                ->label('Nomor Kartu BPJS / KIS (13 Digit)')
                                ->length(13)
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('deactivated_date')
                                ->label('Tanggal Dinonaktifkan'),
                            Select::make('reason')
                                ->label('Alasan Penonaktifan')
                                ->options(collect(PbiReason::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->required(),
                        ]),
                    ]),

                Section::make('Kondisi Medis & Fasilitas Kesehatan (RS / Puskesmas)')
                    ->description('Rujukan rumah sakit dan keterangan darurat medis')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('health_facility_name')
                                ->label('Nama Faskes / RS Perujuk')
                                ->placeholder('Contoh: RSUD Ngudi Waluyo Wlingi'),
                            TextInput::make('health_letter_number')
                                ->label('No. Surat Keterangan Rawat / Dokter')
                                ->placeholder('Contoh: 445/892/RSNW/2026'),
                            TextInput::make('decile')
                                ->label('Desil DTSEN (1-10)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10),
                        ]),
                        Textarea::make('eligibility_notes')
                            ->label('Catatan Kelayakan / Diagnosis / Keterangan Medis')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Rekomendasi Dinsos & Proses Kemensos')
                    ->description('Surat rekomendasi dan keputusan kementerian')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('recommendation_number')
                                ->label('Nomor Surat Rekomendasi')
                                ->placeholder('Contoh: 400.9/89/REK-PBI/2026'),
                            DateTimePicker::make('recommendation_issued_at')
                                ->label('Waktu Rekomendasi Terbit'),
                            Select::make('signer_id')
                                ->label('Pejabat Penandatangan')
                                ->options(User::role(['pejabat_penandatangan', 'pimpinan'])->pluck('name', 'id'))
                                ->searchable(),
                        ]),
                        Grid::make(3)->schema([
                            DateTimePicker::make('proposed_to_ministry_at')
                                ->label('Waktu Diusulkan ke Kemensos'),
                            Select::make('ministry_decision')
                                ->label('Keputusan Kemensos')
                                ->options(collect(MinistryDecision::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->default(MinistryDecision::Pending->value)
                                ->required(),
                            DateTimePicker::make('ministry_decided_at')
                                ->label('Waktu Keputusan Kemensos'),
                        ]),
                        DatePicker::make('reactivated_date')
                            ->label('Tanggal KIS / PBI Resmi Aktif Kembali'),
                    ]),
            ]);
    }
}
