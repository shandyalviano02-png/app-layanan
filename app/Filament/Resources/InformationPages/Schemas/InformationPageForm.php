<?php

namespace App\Filament\Resources\InformationPages\Schemas;

use App\Enums\InformationCategory;
use App\Enums\PublishStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class InformationPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->description('Judul, kategori, dan tautan jenis layanan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label('Judul Halaman Informasi')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('Slug URL')
                                ->required()
                                ->unique(ignoreRecord: true),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('category')
                                ->label('Kategori Informasi')
                                ->options(collect(InformationCategory::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->required(),
                            Select::make('service_type_id')
                                ->label('Keterkaitan Jenis Layanan (Opsional)')
                                ->relationship('serviceType', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                    ]),

                Section::make('Konten & Panduan Layanan')
                    ->description('Rincian deskripsi, persyaratan permohonan, dan alur prosedur')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Deskripsi Layanan / Program')
                            ->columnSpanFull(),
                        RichEditor::make('requirements')
                            ->label('Persyaratan Dokumen / Berkas')
                            ->columnSpanFull(),
                        RichEditor::make('procedure')
                            ->label('Prosedur & Alur Pelayanan')
                            ->columnSpanFull(),
                    ]),

                Section::make('Waktu, Lokasi & Kontak')
                    ->description('Informasi jam operasional dan kontak pelayanan')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('service_hours')
                                ->label('Jam Layanan')
                                ->placeholder('Contoh: Senin - Kamis 08.00 - 15.00 WIB'),
                            TextInput::make('location')
                                ->label('Lokasi Pelayanan')
                                ->placeholder('Contoh: Mal Pelayanan Publik (MPP) / Kantor Dinsos'),
                            TextInput::make('contact')
                                ->label('Kontak / Hotline')
                                ->placeholder('Contoh: (0342) 801234 / 08123456789'),
                        ]),
                    ]),

                Section::make('Status Publikasi')
                    ->description('Pengaturan visibilitas dan penanggung jawab halaman')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('publish_status')
                                ->label('Status Publikasi')
                                ->options(collect(PublishStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->default(PublishStatus::Draft->value)
                                ->required(),
                            DateTimePicker::make('published_at')
                                ->label('Tanggal Publikasi')
                                ->default(now()),
                            Select::make('manager_id')
                                ->label('Penanggung Jawab Konten')
                                ->relationship('manager', 'name')
                                ->default(fn () => auth()->id())
                                ->searchable()
                                ->required(),
                        ]),
                    ]),
            ]);
    }
}
