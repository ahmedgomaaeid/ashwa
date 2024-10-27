<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SellerResource\Pages;
use App\Models\User; // Ensure this is the correct model
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SellerResource extends Resource
{
    protected static ?string $model = User::class; // Ensure this is the correct model

    protected static ?string $navigationIcon = 'heroicon-o-user-group'; // Optional: Use a Heroicon icon in the navigation menu

    protected static ?string $navigationLabel = 'Sellers'; // Set the navigation label to "Sellers"

    protected static ?int $navigationSort = 1; // Optional: Set the order in the navigation menu

    protected static ?string $navigationGroup = 'Users'; // Optional: Group the resource in the navigation menu

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
                    ->unique(User::class, 'email', ignoreRecord: true),
                Forms\Components\TextInput::make('phone')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        '0' => 'User',
                        '1' => 'Seller',
                        '2' => 'Admin',
                    ])
                    ->default('1')
                    ->required(),
                Forms\Components\TextInput::make('discount')
                    ->placeholder('special percentage'),
                Forms\Components\DatePicker::make('finished_after')
                    ->placeholder('special date'),

                Forms\Components\Select::make('verified')
                    ->options([
                        '0' => 'No',
                        '1' => 'Yes',
                    ])
                    ->default('0')
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateSeller) // Only required on create
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateSeller), // Only visible on create
            ])
            ->columns(2); // Organize form fields in two columns
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('User Type')
                    ->getStateUsing(function ($record) {
                        return 'Seller';
                    })
                    ->colors([
                        'yellow' => 'Seller',
                    ])
                    ->sortable(),
                //if discount is null show default word
                Tables\Columns\TextColumn::make('discount')->default('default')->sortable(),
                Tables\Columns\TextColumn::make('finished_after')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('verified')->boolean()->label('Verified')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellers::route('/'),
            'create' => Pages\CreateSeller::route('/create'),
            'edit' => Pages\EditSeller::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 1);
    }
}
