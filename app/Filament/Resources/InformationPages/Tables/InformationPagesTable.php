<?php

namespace App\Filament\Resources\InformationPages\Tables;

use App\Enums\InformationCategory;
use App\Enums\PublishStatus;
use App\Models\InformationPage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InformationPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Halaman')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof InformationCategory ? $state->label() : ($state ? InformationCategory::tryFrom((string) $state)?->label() ?? $state : '-'))
                    ->color('info')
                    ->sortable(),
                TextColumn::make('serviceType.name')
                    ->label('Terkait Layanan')
                    ->searchable()
                    ->placeholder('Umum / Tanpa Kaitan'),
                TextColumn::make('publish_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof PublishStatus ? $state->label() : ($state ? PublishStatus::tryFrom((string) $state)?->label() ?? $state : '-'))
                    ->color(fn ($state) => match ($state instanceof PublishStatus ? $state->value : (string) $state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        default => 'secondary',
                    }),
                TextColumn::make('published_at')
                    ->label('Tgl Publikasi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('manager.name')
                    ->label('Pengelola')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori Informasi')
                    ->options(collect(InformationCategory::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                SelectFilter::make('publish_status')
                    ->label('Status Publikasi')
                    ->options(collect(PublishStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('publish')
                    ->label('Terbitkan')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->color('success')
                    ->visible(fn (InformationPage $record) => $record->publish_status !== PublishStatus::Published)
                    ->action(function (InformationPage $record) {
                        $record->update([
                            'publish_status' => PublishStatus::Published,
                            'published_at' => $record->published_at ?? now(),
                        ]);
                        Notification::make()->title('Halaman informasi berhasil diterbitkan')->success()->send();
                    }),

                Action::make('archive')
                    ->label('Arsipkan')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->color('gray')
                    ->visible(fn (InformationPage $record) => $record->publish_status === PublishStatus::Published)
                    ->action(function (InformationPage $record) {
                        $record->update([
                            'publish_status' => PublishStatus::Archived,
                        ]);
                        Notification::make()->title('Halaman diarsipkan')->info()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
