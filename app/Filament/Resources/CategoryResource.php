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

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'Category';

    protected static ?string $navigationLabel = 'Categories';

    protected static ?string $recordTitleAttribute = 'data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'data')
                    ->label('Parent Category')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                    
                Forms\Components\TextInput::make('data')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                    
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('categories')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),
                    
                Tables\Columns\TextColumn::make('data')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('parent.data')
                    ->label('Parent Category')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
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
                Tables\Filters\SelectFilter::make('parent_id')
                    ->relationship('parent', 'data')
                    ->label('Parent Category')
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
                Tables\Grouping\Group::make('parent.data')
                    ->label('Parent Category')
                    ->collapsible(),
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



    public static function getPages(): array
    {
        return [
            'index' => Pages\Category\ListCategory::route('/'),
            'create' => Pages\Category\CreateCategory::route('/create'),
            //'view' => Pages\Category\ViewCategory::route('/{record}'),
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