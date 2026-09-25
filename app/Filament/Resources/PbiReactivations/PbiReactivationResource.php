<?php

namespace App\Filament\Resources\PbiReactivations;

use App\Filament\Resources\PbiReactivations\Pages\CreatePbiReactivation;
use App\Filament\Resources\PbiReactivations\Pages\EditPbiReactivation;
use App\Filament\Resources\PbiReactivations\Pages\ListPbiReactivations;
use App\Filament\Resources\PbiReactivations\Pages\ViewPbiReactivation;
use App\Filament\Resources\PbiReactivations\Schemas\PbiReactivationForm;
use App\Filament\Resources\PbiReactivations\Schemas\PbiReactivationInfolist;
use App\Filament\Resources\PbiReactivations\Tables\PbiReactivationsTable;
use App\Models\PbiReactivation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PbiReactivationResource extends Resource
{
    protected static ?string $model = PbiReactivation::class;

    protected static string|UnitEnum|null $navigationGroup = 'Layanan 2 — Reaktivasi PBI-JK';

    protected static ?string $navigationLabel = 'Usulan Reaktivasi PBI-JK';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['serviceRequest.village.district', 'signer']);
        $user = auth()->user();

        if (! $user) {
            return $query;
        }

        if ($user->hasRole('operator_wilayah')) {
            if ($user->village_id) {
                $query->whereHas('serviceRequest', fn ($q) => $q->where('village_id', $user->village_id));
            } elseif ($user->district_id) {
                $query->whereHas('serviceRequest.village', fn ($q) => $q->where('district_id', $user->district_id));
            }
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return PbiReactivationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PbiReactivationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PbiReactivationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPbiReactivations::route('/'),
            'create' => CreatePbiReactivation::route('/create'),
            'view' => ViewPbiReactivation::route('/{record}'),
            'edit' => EditPbiReactivation::route('/{record}/edit'),
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
