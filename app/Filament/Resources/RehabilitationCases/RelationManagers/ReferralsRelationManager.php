<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use App\Enums\ReferralStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReferralsRelationManager extends RelationManager
{
    protected static string $relationship = 'referrals';

    protected static ?string $title = 'Surat & Lembaga Rujukan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->components([
                    TextInput::make('referral_number')
                        ->label('Nomor Surat Rujukan')
                        ->default(fn () => 'RUJ-'.date('Ymd').'-'.rand(100, 999))
                        ->required(),
                    Select::make('referral_institution_id')
                        ->label('Lembaga / Panti Tujuan Rujukan')
                        ->relationship('referralInstitution', 'name')
                        ->required(),
                    DatePicker::make('referral_date')
                        ->label('Tanggal Rujukan')
                        ->default(now())
                        ->required(),
                ]),
                Select::make('status')
                    ->label('Status Rujukan')
                    ->options(collect(ReferralStatus::cases())->mapWithKeys(fn ($case) => [$case->value => ucfirst($case->value)]))
                    ->default(ReferralStatus::Draft->value)
                    ->required(),
                Textarea::make('service_result')
                    ->label('Hasil / Perkembangan dari Lembaga Rujukan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('referral_number')
            ->columns([
                TextColumn::make('referral_number')->label('No. Rujukan')->searchable(),
                TextColumn::make('referralInstitution.name')->label('Lembaga Tujuan')->searchable(),
                TextColumn::make('referral_date')->label('Tanggal')->date('d M Y'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('service_result')->label('Hasil')->limit(40),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Buat Rujukan Baru')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['officer_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
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
