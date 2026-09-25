<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use App\Enums\DocumentVerificationStatus;
use App\Models\ServiceRequestDocument;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Dokumen Persyaratan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_requirement_id')
                    ->label('Persyaratan Dokumen')
                    ->relationship('serviceRequirement', 'name')
                    ->required(),
                FileUpload::make('file_path')
                    ->label('Unggah Berkas')
                    ->disk('public')
                    ->directory('service_documents')
                    ->storeFileNamesIn('original_name')
                    ->required(),
                TextInput::make('original_name')
                    ->label('Nama Asli Dokumen'),
                Select::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options(collect(DocumentVerificationStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->default(DocumentVerificationStatus::Pending->value),
                TextInput::make('notes')
                    ->label('Catatan Petugas Pemeriksa'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('original_name')
            ->columns([
                TextColumn::make('serviceRequirement.name')
                    ->label('Persyaratan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('original_name')
                    ->label('Nama Berkas')
                    ->searchable(),
                TextColumn::make('verification_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof DocumentVerificationStatus ? $state->label() : (DocumentVerificationStatus::tryFrom((string) $state)?->label() ?? $state))
                    ->color(fn ($state) => match ($state instanceof DocumentVerificationStatus ? $state->value : (string) $state) {
                        'valid' => 'success',
                        'revision_needed' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('notes')
                    ->label('Catatan'),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Dokumen'),
            ])
            ->recordActions([
                Action::make('mark_valid')
                    ->label('Valid')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->action(function (ServiceRequestDocument $record) {
                        $record->update(['verification_status' => DocumentVerificationStatus::Valid]);
                        Notification::make()->title('Dokumen ditandai Valid')->success()->send();
                    }),
                Action::make('mark_revision_needed')
                    ->label('Perlu Perbaikan')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->form([
                        TextInput::make('notes')->label('Catatan Perbaikan Dokumen')->required(),
                    ])
                    ->action(function (ServiceRequestDocument $record, array $data) {
                        $record->update([
                            'verification_status' => DocumentVerificationStatus::RevisionNeeded,
                            'notes' => $data['notes'],
                        ]);
                        Notification::make()->title('Dokumen ditandai Perlu Perbaikan')->warning()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
