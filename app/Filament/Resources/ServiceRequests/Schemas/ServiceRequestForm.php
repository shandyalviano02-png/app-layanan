<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Enums\ServiceRequestStatus;
use App\Models\District;
use App\Models\User;
use App\Models\Village;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Pemohon')
                    ->description('Data kependudukan pemohon layanan')
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('applicant_name')
                                ->label('Nama Lengkap')
                                ->required(),
                            TextInput::make('applicant_nik')
                                ->label('NIK Pemohon')
                                ->length(16)
                                ->required(),
                            TextInput::make('family_card_number')
                                ->label('Nomor Kartu Keluarga')
                                ->length(16)
                                ->required(),
                        ]),
                        Grid::make(3)->components([
                            TextInput::make('phone')
                                ->label('Nomor Telepon/HP')
                                ->tel()
                                ->required(),
                            Select::make('district_id')
                                ->label('Kecamatan')
                                ->options(District::pluck('name', 'id'))
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(fn ($set) => $set('village_id', null)),
                            Select::make('village_id')
                                ->label('Desa / Kelurahan')
                                ->options(fn ($get) => Village::when($get('district_id'), fn ($q, $d) => $q->where('district_id', $d))->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ]),
                        Textarea::make('address')
                            ->label('Alamat Domisili Lengkap')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Detail Permohonan Layanan')
                    ->description('Informasi jenis permohonan dan nomor tiket')
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('request_number')
                                ->label('Nomor Tiket')
                                ->default(fn () => 'REQ-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -5)))
                                ->disabled()
                                ->dehydrated(),
                            Select::make('service_type_id')
                                ->label('Jenis Layanan')
                                ->relationship('serviceType', 'name')
                                ->required(),
                            DateTimePicker::make('submitted_at')
                                ->label('Waktu Diajukan')
                                ->default(now())
                                ->required(),
                        ]),
                        Grid::make(2)->components([
                            Toggle::make('is_priority')
                                ->label('Prioritas Khusus (Kedaruratan Medis / Lansia / Disabilitas)')
                                ->live(),
                            Textarea::make('priority_reason')
                                ->label('Alasan Kedaruratan/Prioritas')
                                ->visible(fn ($get) => $get('is_priority')),
                        ]),
                    ]),

                Section::make('Penugasan & Status Pelayanan')
                    ->description('Pengelolaan status dan petugas penanggung jawab')
                    ->components([
                        Grid::make(3)->components([
                            Select::make('work_unit_id')
                                ->label('Bidang / Unit Kerja')
                                ->relationship('workUnit', 'name'),
                            Select::make('officer_id')
                                ->label('Petugas Ditugaskan')
                                ->options(User::role(['petugas_dinsos', 'administrator'])->pluck('name', 'id'))
                                ->searchable(),
                            Select::make('status')
                                ->label('Status Terkini')
                                ->options(collect(ServiceRequestStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->default(ServiceRequestStatus::Submitted->value)
                                ->required(),
                        ]),
                        Grid::make(2)->components([
                            Textarea::make('verification_result')
                                ->label('Hasil Verifikasi Berkas/Data SIKS-NG'),
                            Textarea::make('officer_notes')
                                ->label('Catatan Petugas Internal'),
                        ]),
                        Grid::make(2)->components([
                            Textarea::make('assessment_notes')
                                ->label('Catatan Hasil Asesmen Lapangan'),
                            Textarea::make('service_result')
                                ->label('Hasil Akhir Pelayanan'),
                        ]),
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan (Jika ditolak)')
                            ->columnSpanFull(),
                        DateTimePicker::make('completed_at')
                            ->label('Waktu Penyelesaian'),
                    ]),
            ]);
    }
}
