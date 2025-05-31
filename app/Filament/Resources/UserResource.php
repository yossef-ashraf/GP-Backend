<?php

namespace App\Filament\Resources;

use App\Models\User;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                Forms\Components\DatePicker::make('date_of_birth'),
                Forms\Components\Select::make('role')
                    ->options([
                        'user' => 'User',
                        'admin' => 'Admin',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
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
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'primary',
                        'female' => 'success',
                        'other' => 'danger',    
                    }),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'user' => 'success',
                    }),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
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
                SelectFilter::make('role')
                    ->options([
                        'user' => 'User',
                        'admin' => 'Admin',
                    ])
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->multiple()
                    ->searchable(),
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
                Filter::make('orders_count')
                    ->form([
                        Forms\Components\TextInput::make('min_orders')
                            ->numeric()
                            ->placeholder('Min Orders'),
                        Forms\Components\TextInput::make('max_orders')
                            ->numeric()
                            ->placeholder('Max Orders'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_orders'],
                                fn (Builder $query, $count): Builder => $query->has('orders', '>=', $count),
                            )
                            ->when(
                                $data['max_orders'],
                                fn (Builder $query, $count): Builder => $query->has('orders', '<=', $count),
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
                Components\Section::make('User Information')
                    ->schema([
                        Components\TextEntry::make('name'),
                        Components\TextEntry::make('email'),
                        Components\TextEntry::make('phone'),
                        Components\TextEntry::make('gender'),
                        Components\TextEntry::make('date_of_birth')
                            ->date(),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\User\OrdersRelationManager::class,
            RelationManagers\User\AddressesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\User\ListUser::route('/'),
            'create' => Pages\User\CreateUser::route('/create'),
            'edit' => Pages\User\EditUser::route('/{record}/edit'),
        ];
    }
}