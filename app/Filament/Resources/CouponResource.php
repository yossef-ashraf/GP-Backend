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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\TernaryFilter;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Orders Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Coupon';

    protected static ?string $navigationLabel = 'Coupons';

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('discount_value')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('valid_from')
                    ->required(),
                Forms\Components\DatePicker::make('valid_to')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\TextInput::make('usage_limit')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('usage_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('min_order_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
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
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'success',
                        'fixed' => 'primary',
                    }),
                Tables\Columns\TextColumn::make('valid_from')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_to')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('usage_limit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_order_amount')
                    ->money('EGP')
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
                SelectFilter::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ])
                    ->multiple()
                    ->searchable(),
                Filter::make('is_active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                Filter::make('valid_from')
                    ->form([
                        DatePicker::make('valid_from'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['valid_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('valid_from', '>=', $date),
                            );
                    }),
                Filter::make('valid_to')
                    ->form([
                        DatePicker::make('valid_to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['valid_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('valid_to', '<=', $date),
                            );
                    }),
                Filter::make('usage_count')
                    ->form([
                        Forms\Components\TextInput::make('min_usage')
                            ->numeric()
                            ->placeholder('Min Usage'),
                        Forms\Components\TextInput::make('max_usage')
                            ->numeric()
                            ->placeholder('Max Usage'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_usage'],
                                fn (Builder $query, $count): Builder => $query->where('usage_count', '>=', $count),
                            )
                            ->when(
                                $data['max_usage'],
                                fn (Builder $query, $count): Builder => $query->where('usage_count', '<=', $count),
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