<?php

namespace App\Filament\Resources\RehabilitationCases\Schemas;

use App\Enums\HandlingType;
use App\Enums\RehabilitationCaseStatus;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RehabilitationCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Kasus & Klien')
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('case_number')
                                ->label('Nomor Kasus')
                                ->default(fn () => 'REH-'.date('Ymd').'-'.rand(100, 999))
                                ->required(),
                            Select::make('client_id')
                                ->label('Nama Klien (PPKS)')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DateTimePicker::make('received_at')
                                ->label('Tanggal & Waktu Masuk')
                                ->default(now())
                                ->required(),
                        ]),
                        Grid::make(2)->components([
                            Select::make('service_request_id')
                                ->label('Asal Permohonan Layanan (Opsional)')
                                ->relationship('serviceRequest', 'request_number')
                                ->searchable(),
                            Select::make('complaint_id')
                                ->label('Asal Laporan Pengaduan (Opsional)')
                                ->relationship('complaint', 'ticket_number')
                                ->searchable(),
                        ]),
                    ]),

                Section::make('Penugasan & Penanganan Kasus')
                    ->components([
                        Grid::make(3)->components([
                            Select::make('officer_id')
                                ->label('Pekerja Sosial / Petugas Penanggung Jawab')
                                ->options(User::role(['petugas_dinsos', 'administrator'])->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('handling_type')
                                ->label('Jenis Penanganan')
                                ->options(collect(HandlingType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->default(HandlingType::Direct->value)
                                ->required(),
                            Select::make('status')
                                ->label('Status Penanganan')
                                ->options(collect(RehabilitationCaseStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->default(RehabilitationCaseStatus::Received->value)
                                ->required(),
                        ]),
                        Textarea::make('handling_result')
                            ->label('Hasil Akhir Penanganan / Rekomendasi Terminasi')
                            ->columnSpanFull(),
                        DateTimePicker::make('closed_at')
                            ->label('Tanggal Kasus Ditutup / Selesai'),
                    ]),
            ]);
    }
}
