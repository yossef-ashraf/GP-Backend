<?php

namespace App\Filament\Resources;

use App\Models\Area;
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

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Shipping Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Area';

    protected static ?string $navigationLabel = 'Areas';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Components\Section::make('Area Information')
                    ->schema([
                        Components\TextEntry::make('name'),
                        Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])->columns(2),
                Components\Section::make('Shipping Values')
                    ->schema([
                        Components\RepeatableEntry::make('shippingValues')
                            ->schema([
                                Components\TextEntry::make('value')
                                    ->money(),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Area\ListArea::route('/'),
            'create' => Pages\Area\CreateArea::route('/create'),
            'edit' => Pages\Area\EditArea::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->withoutGlobalScopes([
    //             SoftDeletingScope::class,
    //         ]);
    // }
}