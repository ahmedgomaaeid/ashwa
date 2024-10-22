<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductImageResource\Pages;
use App\Models\ProductImage;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class ProductImageResource extends Resource
{
    protected static ?string $model = ProductImage::class;

    //hide it from the sidebar
    protected static ?string $navigationGroup = null;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }




    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if ($productId = request()->get('record')) {
            $query->where('product_id', $productId);
        }

        return $query;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Hidden field to automatically set the product_id
                Forms\Components\Hidden::make('product_id')
                    ->default(fn () => request()->get('record'))
                    ->required(),

                // Image upload field
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Display the uploaded image
                Tables\Columns\ImageColumn::make('image'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Actions for editing and deleting images
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Bulk delete action
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductImages::route('/'),
            'create' => Pages\CreateProductImage::route('/create'),
            'edit' => Pages\EditProductImage::route('/{record}/edit'),
        ];
    }
}
