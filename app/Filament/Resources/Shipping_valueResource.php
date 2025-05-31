<?php

namespace App\Filament\Resources;

use App\Models\ShippingValue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\TernaryFilter;

class Shipping_valueResource extends Resource
{
    protected static ?string $model = ShippingValue::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Shipping Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Shipping Value';

    protected static ?string $navigationLabel = 'Shipping Values';

    protected static ?string $recordTitleAttribute = 'value';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('area_id')
                    ->relationship('area', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('area')
                    ->relationship('area', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('value')
                    ->form([
                        Forms\Components\TextInput::make('min_value')
                            ->numeric()
                            ->placeholder('Min Value'),
                        Forms\Components\TextInput::make('max_value')
                            ->numeric()
                            ->placeholder('Max Value'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_value'],
                                fn (Builder $query, $value): Builder => $query->where('value', '>=', $value),
                            )
                            ->when(
                                $data['max_value'],
                                fn (Builder $query, $value): Builder => $query->where('value', '<=', $value),
                            );
                    }),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Shipping Value Information')
                    ->schema([
                        Components\TextEntry::make('area.name')
                            ->label('Area'),
                            
                        Components\TextEntry::make('value')
                            ->money(),
                            
                        Components\TextEntry::make('created_at')
                            ->dateTime(),
                            
                        Components\TextEntry::make('updated_at')
                            ->dateTime()
                    ])->columns(2)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\Shipping_value\AreaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Shipping_value\ListShipping_value::route('/'),
            'create' => Pages\Shipping_value\CreateShipping_value::route('/create'),
            'edit' => Pages\Shipping_value\EditShipping_value::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}