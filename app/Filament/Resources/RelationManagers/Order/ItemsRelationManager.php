<?php

namespace App\Filament\Resources\RelationManagers\Order;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('variation_id')
                    ->relationship('variation', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                    
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                    
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix(config('settings.currency_symbol', '$')),
                    
                Forms\Components\TextInput::make('total_amount')
                    ->numeric()
                    ->required()
                    ->prefix(config('settings.currency_symbol', '$')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('variation.name')
                    ->searchable()
                    ->placeholder('No variation'),
                    
                Tables\Columns\TextColumn::make('quantity'),
                    
                Tables\Columns\TextColumn::make('price')
                    ->money(),
                    
                Tables\Columns\TextColumn::make('total_amount')
                    ->money(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}