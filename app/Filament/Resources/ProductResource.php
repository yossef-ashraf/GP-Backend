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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $modelLabel = 'Product';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                    
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpanFull(),
                                    
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'simple' => 'Simple Product',
                                        'variable' => 'Variable Product',
                                    ])
                                    ->required()
                                    ->live()
                                    ->columnSpanFull(),
                                    
                                Forms\Components\TextInput::make('sku')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100)
                                    ->columnSpanFull(),
                                    
                                Forms\Components\Textarea::make('short_description')
                                    ->columnSpanFull(),
                                    
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Pricing')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->required()
                                    ->prefix(config('settings.currency_symbol', '$')),
                                    
                                Forms\Components\TextInput::make('sale_price')
                                    ->numeric()
                                    ->prefix(config('settings.currency_symbol', '$'))
                                    ->gt('price'),
                                    
                                Forms\Components\Toggle::make('sold_individually')
                                    ->label('Sold individually')
                                    ->default(false),
                            ])->columns(3),
                            
                        Forms\Components\Tabs\Tab::make('Inventory')
                            ->schema([
                                Forms\Components\Select::make('stock_status')
                                    ->options([
                                        'in_stock' => 'In Stock',
                                        'out_of_stock' => 'Out of Stock',
                                        'on_backorder' => 'On Backorder',
                                    ])
                                    ->required(),
                                    
                                Forms\Components\TextInput::make('stock_qty')
                                    ->numeric()
                                    ->minValue(0),
                                    
                                Forms\Components\Toggle::make('manage_stock')
                                    ->label('Manage stock?')
                                    ->live(),
                            ])->columns(3),
                            
                        Forms\Components\Tabs\Tab::make('Categories')
                            ->schema([
                                Forms\Components\Select::make('categories')
                                    ->relationship('categories', 'data')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Images')
                            ->schema([
                                Forms\Components\FileUpload::make('images')
                                    ->image()
                                    ->multiple()
                                    ->directory('products')
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Variations')
                            ->visible(fn ($get) => $get('type') === 'variable')
                            ->schema([
                                Forms\Components\Repeater::make('variations')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('sku')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(100),
                                            
                                        Forms\Components\TextInput::make('price')
                                            ->numeric()
                                            ->required(),
                                            
                                        Forms\Components\TextInput::make('sale_price')
                                            ->numeric(),
                                            
                                        Forms\Components\TextInput::make('stock_qty')
                                            ->numeric()
                                            ->minValue(0),
                                            
                                        Forms\Components\Select::make('stock_status')
                                            ->options([
                                                'in_stock' => 'In Stock',
                                                'out_of_stock' => 'Out of Stock',
                                                'on_backorder' => 'On Backorder',
                                            ]),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->stacked()
                    ->limit(1)
                    ->circular(),
                    
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'simple' => 'info',
                        'variable' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable()
                    ->description(fn (Product $record) => $record->sale_price ? 
                        'Sale: ' . ($record->sale_price) : null),
                    
                Tables\Columns\TextColumn::make('stock_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_stock' => 'success',
                        'out_of_stock' => 'danger',
                        'on_backorder' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('stock_qty')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('categories.data')
                    ->badge()
                    ->separator(','),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'simple' => 'Simple',
                        'variable' => 'Variable',
                    ]),
                Tables\Filters\SelectFilter::make('categories')
                    ->relationship('categories', 'data')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('stock_status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'out_of_stock' => 'Out of Stock',
                        'on_backorder' => 'On Backorder',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('type')
                    ->label('Product Type')
                    ->collapsible(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Product Information')
                    ->schema([
                        Components\TextEntry::make('name'),
                        Components\TextEntry::make('type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'simple' => 'info',
                                'variable' => 'success',
                                default => 'gray',
                            }),
                        Components\TextEntry::make('price')
                            ->money(),
                        Components\TextEntry::make('sale_price')
                            ->money(),
                        Components\TextEntry::make('stock_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'in_stock' => 'success',
                                'out_of_stock' => 'danger',
                                'on_backorder' => 'warning',
                                default => 'gray',
                            }),
                        Components\TextEntry::make('stock_qty'),
                        Components\TextEntry::make('short_description')
                            ->columnSpanFull(),
                        Components\TextEntry::make('description')
                            ->html()
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Components\Section::make('Categories')
                    ->schema([
                        Components\RepeatableEntry::make('categories')
                            ->schema([
                                Components\TextEntry::make('data'),
                            ])
                            ->grid(3),
                    ]),
                    
                Components\Section::make('Images')
                    ->schema([
                        Components\ImageEntry::make('images')
                            ->stacked()
                            ->height(150)
                            ->width(150),
                    ]),
                    
                Components\Section::make('Variations')
                    ->hidden(fn ($record) => $record->type !== 'variable')
                    ->schema([
                        Components\RepeatableEntry::make('variations')
                            ->schema([
                                Components\TextEntry::make('sku'),
                                Components\TextEntry::make('price')
                                    ->money(),
                                Components\TextEntry::make('sale_price')
                                    ->money(),
                                Components\TextEntry::make('stock_status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'in_stock' => 'success',
                                        'out_of_stock' => 'danger',
                                        'on_backorder' => 'warning',
                                        default => 'gray',
                                    }),
                                Components\TextEntry::make('stock_qty'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\Product\CategoriesRelationManager::class,
            RelationManagers\Product\VariationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Product\ListProduct::route('/'),
            'create' => Pages\Product\CreateProduct::route('/create'),
            'view' => Pages\Product\ViewProduct::route('/{record}'),
            'edit' => Pages\Product\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['categories', 'variations'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}