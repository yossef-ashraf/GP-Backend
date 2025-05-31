<?php

namespace App\Filament\Resources;

use App\Models\Order;
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

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $modelLabel = 'Order';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationGroup = 'Orders Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('area_id')
                            ->relationship('area', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $shippingValue = \App\Models\ShippingValue::where('area_id', $state)->first();
                                    $set('shipping_cost', $shippingValue ? $shippingValue->value : 0);
                                }
                            }),
                            
                        Forms\Components\TextInput::make('shipping_cost')
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->prefix(config('settings.currency_symbol', '$')),
                            
                        Forms\Components\Select::make('coupon_id')
                            ->relationship('coupon', 'code')
                            ->searchable()
                            ->disabled()
                            ->preload()
                            ->nullable(),
                            
                            Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->required()
                            
                            ->rows(3),
                        
                            
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'credit_card' => 'Credit Card',
                                'paypal' => 'PayPal',
                            ])
                            ->required(),
                            
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->required()
                            ->prefix(config('settings.currency_symbol', '$')),
                            
                        Forms\Components\Select::make('status')
                            ->options([
                                'pre-pay' => 'Pre-Pay',
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'payed' => 'Payed',
                            ])
                            ->required(),
                            
                        Forms\Components\TextInput::make('tracking_number'),
                            
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('area.name')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->money()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_amount')
                    ->money()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'gray',
                        'credit_card' => 'primary',
                        'paypal' => 'blue',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pre-pay' => 'info',
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'payed' => 'green',
                        default => 'gray',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pre-pay' => 'Pre Pay',
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'payed' => 'Payed',
                    ])
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'credit_card' => 'Credit Card',
                        'paypal' => 'PayPal',
                    ])
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('area')
                    ->relationship('area', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
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
                Filter::make('total_amount')
                    ->form([
                        Forms\Components\TextInput::make('min_amount')
                            ->numeric()
                            ->placeholder('Min Amount'),
                        Forms\Components\TextInput::make('max_amount')
                            ->numeric()
                            ->placeholder('Max Amount'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
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
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('status')
                    ->label('Status')
                    ->collapsible(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Order Information')
                    ->schema([
                        Components\TextEntry::make('user.name')
                            ->label('Customer'),
                            
                        Components\TextEntry::make('coupon.code')
                            ->label('Coupon')
                            ->placeholder('No coupon used'),
                            
                            Components\TextEntry::make('address')
                            ->label('Address'),
                            
                        Components\TextEntry::make('payment_method')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'cash' => 'gray',
                                'credit_card' => 'primary',
                                'paypal' => 'blue',
                                default => 'gray',
                            }),
                            
                        Components\TextEntry::make('total_amount')
                            ->money(),
                            
                        Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pre-pay' => 'info',
                                'pending' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                'payed' => 'green',
                                default => 'gray',
                            }),
                            
                        Components\TextEntry::make('tracking_number')
                            ->placeholder('No tracking number'),
                            
                        Components\TextEntry::make('notes')
                            ->columnSpanFull(),
                            
                        Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])->columns(2),
                    
                Components\Section::make('Order Items')
                    ->schema([
                        Components\RepeatableEntry::make('items')
                            ->schema([
                                Components\TextEntry::make('product.name')
                                    ->label('Product'),
                                    
                                Components\TextEntry::make('variation.name')
                                    ->label('Variant')
                                    ->placeholder('No variation'),
                                    
                                Components\TextEntry::make('quantity'),
                                    
                                Components\TextEntry::make('price')
                                    ->money(),
                                    
                                Components\TextEntry::make('total_amount')
                                    ->money(),
                            ])
                            ->columns(3),
                    ]),
                    
                Components\Section::make('Order Notes')
                    ->schema([
                        Components\RepeatableEntry::make('notes')
                            ->schema([
                                Components\TextEntry::make('notes')
                                    ->columnSpanFull(),
                                    
                                Components\TextEntry::make('created_at')
                                    ->dateTime(),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\Order\ItemsRelationManager::class,
            RelationManagers\Order\NotesRelationManager::class,
            RelationManagers\Order\AddresRelationManager::class,
            RelationManagers\Order\CouponRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Order\ListOrder::route('/'),
            'create' => Pages\Order\CreateOrder::route('/create'),
            'edit' => Pages\Order\EditOrder::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->with(['user', 'address', 'coupon', 'items', 'notes']);
    // }
}
