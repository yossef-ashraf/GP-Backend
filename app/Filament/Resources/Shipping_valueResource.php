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

class Shipping_valueResource extends Resource
{
    protected static ?string $model = ShippingValue::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

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
                    ->preload()
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->required()
                    ->prefix(config('settings.currency_symbol'))
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('area.name')
                    ->label('Area')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('value')
                    ->numeric()
                    ->sortable()
                    ->money(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('area_id')
                    ->relationship('area', 'name')
                    ->label('Filter by Area')
            ])
            ->actions([
                //Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc')
            ->groups([
                Tables\Grouping\Group::make('area.name')
                    ->label('Area')
                    ->collapsible(),
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
            // يمكنك إضافة RelationManagers هنا إذا لزم الأمر
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Shipping_value\ListShipping_value::route('/'),
            'create' => Pages\Shipping_value\CreateShipping_value::route('/create'),
            //'view' => Pages\Shipping_value\ViewShipping_value::route('/{record}'),
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