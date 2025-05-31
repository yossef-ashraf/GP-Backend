<?php

namespace App\Filament\Resources;

use App\Models\Category;
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

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Products Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Category';

    protected static ?string $navigationLabel = 'Categories';

    protected static ?string $recordTitleAttribute = 'data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('data')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('image')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('data')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->square(),
                Tables\Columns\TextColumn::make('products_count')
                ->label('Parent Category'),

                Tables\Columns\TextColumn::make('parent.data')
                ->counts('products')
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
                Filter::make('products_count')
                    ->form([
                        Forms\Components\TextInput::make('min_products')
                            ->numeric()
                            ->placeholder('Min Products'),
                        Forms\Components\TextInput::make('max_products')
                            ->numeric()
                            ->placeholder('Max Products'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_products'],
                                fn (Builder $query, $count): Builder => $query->has('products', '>=', $count),
                            )
                            ->when(
                                $data['max_products'],
                                fn (Builder $query, $count): Builder => $query->has('products', '<=', $count),
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
                Components\Section::make('Category Information')
                    ->schema([
                        Components\TextEntry::make('data'),
                        
                        Components\TextEntry::make('parent.data')
                            ->label('Parent Category'),
                            
                            
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Category\ListCategory::route('/'),
            'create' => Pages\Category\CreateCategory::route('/create'),
            'edit' => Pages\Category\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['parent', 'products'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}