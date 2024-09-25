<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Faker\Provider\ar_EG\Text;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->default(now()),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable(),
                TextColumn::make('email')
                ->searchable(),
                TextColumn::make('email_verified_at')
                ->dateTime()
                ->sortable(),
                TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // ini action berbentuk group
                // Tables\Actions\ActionGroup::make([
                //     Tables\Actions\ViewAction::make(),
                //     Tables\Actions\EditAction::make(),
                //     Tables\Actions\DeleteAction::make(),
                // ])
                // ini action sendiri2
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
