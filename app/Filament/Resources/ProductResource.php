<?php

namespace App\Filament\Resources;

use App\Models\Product;
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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Products Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Product';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('image')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('author')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'simple' => 'Simple',
                        'variable' => 'Variable',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('sku')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('sale_price')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('stock_status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'out_of_stock' => 'Out of Stock',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('stock_qty')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'data')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->square(),
                Tables\Columns\TextColumn::make('author')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'simple' => 'success',
                        'variable' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_stock' => 'success',
                        'out_of_stock' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.data')
                    ->listWithLineBreaks()
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
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'simple' => 'Simple',
                        'variable' => 'Variable',
                    ])
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('stock_status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'out_of_stock' => 'Out of Stock',
                    ])
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('categories')
                    ->relationship('categories', 'data')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('price')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->numeric()
                            ->placeholder('Min Price'),
                        Forms\Components\TextInput::make('max_price')
                            ->numeric()
                            ->placeholder('Max Price'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['max_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
                Filter::make('stock_qty')
                    ->form([
                        Forms\Components\TextInput::make('min_qty')
                            ->numeric()
                            ->placeholder('Min Quantity'),
                        Forms\Components\TextInput::make('max_qty')
                            ->numeric()
                            ->placeholder('Max Quantity'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_qty'],
                                fn (Builder $query, $qty): Builder => $query->where('stock_qty', '>=', $qty),
                            )
                            ->when(
                                $data['max_qty'],
                                fn (Builder $query, $qty): Builder => $query->where('stock_qty', '<=', $qty),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\Product\VariationsRelationManager::class,
            RelationManagers\Product\CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Product\ListProduct::route('/'),
            'create' => Pages\Product\CreateProduct::route('/create'),
            'edit' => Pages\Product\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['categories', 'variations']);
        
        return $query;
    }
}