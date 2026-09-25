<?php

namespace App\Filament\Resources\Complaints\Schemas;

use App\Enums\ComplaintStatus;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Laporan')
                    ->description('Nomor tiket dan kategori pengaduan')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('complaint_number')
                                ->label('Nomor Pengaduan')
                                ->default(fn () => 'ADU-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -5)))
                                ->required()
                                ->readOnly(),
                            Select::make('complaint_category_id')
                                ->label('Kategori Pengaduan')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DateTimePicker::make('reported_at')
                                ->label('Waktu Pelaporan')
                                ->default(now())
                                ->required(),
                        ]),
                    ]),

                Section::make('Data Pelapor & Lokasi Kejadian')
                    ->description('Identitas pelapor dan lokasi permasalahan sosial yang diadukan')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('reporter_name')
                                ->label('Nama Pelapor')
                                ->required(),
                            TextInput::make('reporter_phone')
                                ->label('No. WhatsApp / HP Pelapor')
                                ->tel()
                                ->required(),
                            Select::make('reporter_id')
                                ->label('Akun Pengguna (opsional)')
                                ->relationship('reporter', 'name')
                                ->searchable(),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('village_id')
                                ->label('Desa / Kelurahan Kejadian')
                                ->relationship('village', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Textarea::make('location_detail')
                                ->label('Detail Lokasi / Patokan Alamat')
                                ->rows(2)
                                ->required(),
                        ]),
                        Textarea::make('description')
                            ->label('Uraian / Isi Laporan Pengaduan')
                            ->rows(4)
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make('Penanganan & Tindak Lanjut')
                    ->description('Disposisi petugas, verifikasi, dan tindakan yang diambil')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('officer_id')
                                ->label('Petugas yang Ditugaskan')
                                ->options(User::role(['petugas_dinsos', 'administrator'])->pluck('name', 'id'))
                                ->searchable(),
                            Select::make('status')
                                ->label('Status Pengaduan')
                                ->options(collect(ComplaintStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->default(ComplaintStatus::Received->value)
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('verification_result')
                                ->label('Hasil Verifikasi Lapangan')
                                ->rows(3),
                            Textarea::make('action_taken')
                                ->label('Tindakan / Solusi yang Diberikan')
                                ->rows(3),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('duplicate_of_id')
                                ->label('Duplikat dari Laporan No.')
                                ->relationship('duplicateOf', 'complaint_number')
                                ->searchable(),
                            DateTimePicker::make('resolved_at')
                                ->label('Waktu Laporan Diselesaikan'),
                        ]),
                    ]),
            ]);
    }
}
