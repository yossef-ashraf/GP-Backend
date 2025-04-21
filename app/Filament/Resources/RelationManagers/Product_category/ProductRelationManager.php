<?php

namespace App\Filament\Resources\RelationManagers\Product_category;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slug')
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sku')
                    ->maxLength(255),
                Forms\Components\TextInput::make('price'),
                Forms\Components\TextInput::make('sale_price'),
                Forms\Components\TextInput::make('sold_individually'),
                Forms\Components\TextInput::make('stock_status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('stock_qty'),
                Forms\Components\TextInput::make('total_sales')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sold_individually')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sales')
                    ->searchable()
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}