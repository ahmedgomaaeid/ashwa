<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\ImagesRelationManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\Section;
use Filament\Forms;
use Filament\Forms\Components\BelongsToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    // Navigation settings
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationGroup = 'Products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Product name
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                // Product description
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535),

                // Price
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric(),

                // Quantity
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),

                // Delivery fees
                Forms\Components\TextInput::make('delivery_fees')
                    ->required()
                    ->numeric(),

                // Status
                Forms\Components\Select::make('status')
                    ->options([
                        '0' => 'Inactive',
                        '1' => 'Active',
                    ])
                    ->default('1')
                    ->required(),

                // Category select
                BelongsToSelect::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('section_id', null);
                    }),

                // Section select, filtered by selected category
                Select::make('section_id')
                    ->label('Section')
                    ->options(function (callable $get) {
                        $categoryId = $get('category_id');
                        if ($categoryId) {
                            return Section::where('category_id', $categoryId)->pluck('name', 'id');
                        }
                        return [];
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->disabled(fn ($get) => empty($get('category_id'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Status
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->label('Status')
                    ->sortable(),

                // Product name
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                // Price
                Tables\Columns\TextColumn::make('price')
                    ->sortable(),

                // Category name
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                // Section name
                Tables\Columns\TextColumn::make('section.name')
                    ->label('Section')
                    ->sortable(),

                // Optional: Display main image
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Image')
                    ->getStateUsing(function (Product $record) {
                        return $record->images()->first()?->image;
                    })
                    ->disk('public')
                    ->circular(),
            ])
            ->filters([
                // Define filters if necessary
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Optionally, add a view action
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // Register relation managers
    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
        ];
    }

    // Define resource pages
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            //'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
