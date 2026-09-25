<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun & Kredensial')
                    ->description('Informasi login dan hak akses pengguna')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required(),
                            TextInput::make('email')
                                ->label('Alamat Email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('password')
                                ->label('Kata Sandi')
                                ->password()
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->helperText('Kosongkan jika tidak ingin mengubah kata sandi'),
                            Select::make('roles')
                                ->label('Peran / Hak Akses (Role)')
                                ->relationship('roles', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->required(),
                        ]),
                    ]),

                Section::make('Identitas & Penugasan Wilayah')
                    ->description('Data diri dan penempatan kerja')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nik')
                                ->label('NIK (16 Digit)')
                                ->length(16),
                            TextInput::make('phone')
                                ->label('Nomor WhatsApp / HP')
                                ->tel(),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('work_unit_id')
                                ->label('Unit Kerja (Dinsos / OPD)')
                                ->relationship('workUnit', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('district_id')
                                ->label('Kecamatan Penugasan')
                                ->relationship('district', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('village_id')
                                ->label('Desa / Kelurahan Penugasan')
                                ->relationship('village', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                        Toggle::make('is_active')
                            ->label('Status Akun Aktif')
                            ->helperText('Hanya akun aktif yang dapat masuk ke sistem')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }
}
