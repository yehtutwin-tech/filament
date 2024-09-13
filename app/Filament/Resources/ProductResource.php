<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                   ->required()
                   ->maxLength(255),
               Forms\Components\TextInput::make('price')
                   ->numeric()
                   ->prefix('Ks')
                   ->maxValue(100000.00),
               Forms\Components\Select::make('category_id')
                   ->label('Category')
                   ->options(Category::query()->pluck('name', 'id'))
                   ->live(),
               Forms\Components\Select::make('sub_category_id')
                   ->label('Sub Category')
                   ->options(fn (Get $get): Collection => SubCategory::query()
                       ->where('category_id', $get('category_id'))
                       ->pluck('name', 'id')),
               Forms\Components\Textarea::make('description')
                   ->maxLength(65535)
                   ->columnSpan('full'),
               Forms\Components\Checkbox::make('status')->label('Active'),
               Forms\Components\FileUpload::make('image')
                   ->image()
                   ->imageEditor()
                   ->imageEditorAspectRatios([
                       '16:9',
                       '4:3',
                       '1:1',
                   ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price (Ks)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('sub_category.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Filter::make('category_id')
                    ->form([
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->options(Category::query()->pluck('name', 'id'))
                            ->live(),
                        Forms\Components\Select::make('sub_category_id')
                            ->label('Sub Category')
                            ->options(fn (Get $get): Collection => SubCategory::query()
                                ->where('category_id', $get('category_id'))
                                ->pluck('name', 'id')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['category_id'],
                                fn (Builder $query, $data): Builder => $query->where('category_id', '=', $data),
                            )
                            ->when(
                                $data['sub_category_id'],
                                fn (Builder $query, $data): Builder => $query->where('sub_category_id', '=', $data),
                            );
                    }),
                SelectFilter::make('status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
