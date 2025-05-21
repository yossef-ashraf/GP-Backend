<?php

namespace App\Filament\Resources;

use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $modelLabel = 'Coupon';

    protected static ?string $navigationLabel = 'Coupons';

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('discount_value')
                    ->numeric()
                    ->required()
                    ->prefix(fn ($state) => self::getDiscountPrefix())
                    ->columnSpanFull(),
                    
                Forms\Components\Select::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ])
                    ->required()
                    ->live()
                    ->columnSpanFull(),
                    
                Forms\Components\DateTimePicker::make('valid_from')
                    ->required()
                    ->columnSpanFull(),
                    
                Forms\Components\DateTimePicker::make('valid_to')
                    ->required()
                    ->columnSpanFull(),
                    
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }

    protected static function getDiscountPrefix(): ?string
    {
        $type = request()->input('data.discount_type');
        return $type === 'percentage' ? '%' : (config('settings.currency_symbol') ?? '$');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Coupon $record) => $record->name),
                    
                Tables\Columns\TextColumn::make('discount_value')
                    ->formatStateUsing(fn ($state, Coupon $record) => 
                        $record->discount_type === 'percentage' ? 
                        "{$state}%" : 
                        (config('settings.currency_symbol') ?? '$') . $state
                    )
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('valid_from')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('valid_to')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Times Used')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ]),
                Tables\Filters\Filter::make('active')
                    ->label('Active Coupons')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),
                Tables\Filters\Filter::make('expired')
                    ->label('Expired Coupons')
                    ->query(fn (Builder $query) => $query->where('valid_to', '<', now())),
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
            ->defaultSort('valid_to', 'desc')
            ->groups([
                Tables\Grouping\Group::make('discount_type')
                    ->label('Discount Type')
                    ->collapsible(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Coupon Details')
                    ->schema([
                        Components\TextEntry::make('name'),
                        Components\TextEntry::make('code'),
                        Components\TextEntry::make('discount_value')
                            ->formatStateUsing(fn ($state, Coupon $record) => 
                                $record->discount_type === 'percentage' ? 
                                "{$state}%" : 
                                (config('settings.currency_symbol') ?? '$') . $state
                            ),
                        Components\TextEntry::make('discount_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'percentage' => 'info',
                                'fixed' => 'success',
                                default => 'gray',
                            }),
                        Components\TextEntry::make('valid_from')
                            ->dateTime(),
                        Components\TextEntry::make('valid_to')
                            ->dateTime(),
                        Components\IconEntry::make('is_active')
                            ->boolean(),
                        Components\TextEntry::make('orders_count')
                            ->label('Times Used')
                            ->numeric(),
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
            'index' => Pages\Coupon\ListCoupon::route('/'),
            'create' => Pages\Coupon\CreateCoupon::route('/create'),
            //'view' => Pages\Coupon\ViewCoupon::route('/{record}'),
            'edit' => Pages\Coupon\EditCoupon::route('/{record}/edit'),
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