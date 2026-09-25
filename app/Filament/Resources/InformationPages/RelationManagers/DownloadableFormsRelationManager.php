<?php

namespace App\Filament\Resources\InformationPages\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DownloadableFormsRelationManager extends RelationManager
{
    protected static string $relationship = 'downloadableForms';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Formulir / Dokumen')
                    ->placeholder('Contoh: Formulir Permohonan Rekomendasi PBI-JK')
                    ->required(),
                FileUpload::make('file_path')
                    ->label('File Formulir (PDF / DOC)')
                    ->disk('public')
                    ->directory('downloadable_forms')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->required(),
                TextInput::make('version')
                    ->label('Versi Formulir')
                    ->required()
                    ->default('1.0'),
                Toggle::make('is_current')
                    ->label('Versi Berlaku Saat Ini')
                    ->default(true)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Formulir')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('file_path')
                    ->label('Path / File')
                    ->limit(40),
                TextColumn::make('version')
                    ->label('Versi')
                    ->searchable(),
                IconColumn::make('is_current')
                    ->label('Berlaku')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
