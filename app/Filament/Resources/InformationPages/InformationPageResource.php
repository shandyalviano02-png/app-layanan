<?php

namespace App\Filament\Resources\InformationPages;

use App\Filament\Resources\InformationPages\Pages\CreateInformationPage;
use App\Filament\Resources\InformationPages\Pages\EditInformationPage;
use App\Filament\Resources\InformationPages\Pages\ListInformationPages;
use App\Filament\Resources\InformationPages\Pages\ViewInformationPage;
use App\Filament\Resources\InformationPages\RelationManagers\DownloadableFormsRelationManager;
use App\Filament\Resources\InformationPages\RelationManagers\FaqsRelationManager;
use App\Filament\Resources\InformationPages\Schemas\InformationPageForm;
use App\Filament\Resources\InformationPages\Schemas\InformationPageInfolist;
use App\Filament\Resources\InformationPages\Tables\InformationPagesTable;
use App\Models\InformationPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class InformationPageResource extends Resource
{
    protected static ?string $model = InformationPage::class;

    protected static string|UnitEnum|null $navigationGroup = 'Layanan 6 — Informasi';

    protected static ?string $navigationLabel = 'Halaman Informasi & Panduan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function form(Schema $schema): Schema
    {
        return InformationPageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InformationPageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InformationPagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            FaqsRelationManager::class,
            DownloadableFormsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInformationPages::route('/'),
            'create' => CreateInformationPage::route('/create'),
            'view' => ViewInformationPage::route('/{record}'),
            'edit' => EditInformationPage::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
