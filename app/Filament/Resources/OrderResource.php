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

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $modelLabel = 'Order';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?string $recordTitleAttribute = 'id';

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
                            
                        Forms\Components\Select::make('coupon_id')
                            ->relationship('coupon', 'code')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                            
                        Forms\Components\Select::make('address_id')
                            ->relationship('address', 'street')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
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
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
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
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pre-pay' => 'Pre-Pay',
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'payed' => 'Payed',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'credit_card' => 'Credit Card',
                        'paypal' => 'PayPal',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
                            
                        Components\TextEntry::make('address.street')
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
            'view' => Pages\Order\ViewOrder::route('/{record}'),
            'edit' => Pages\Order\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'address', 'coupon', 'items', 'notes'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}