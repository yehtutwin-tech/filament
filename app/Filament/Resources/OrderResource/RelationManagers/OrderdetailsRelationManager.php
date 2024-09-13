<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderdetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderdetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                   ->relationship('product', 'name')
                   ->searchable()
                   ->preload()
                   ->required()
                   ->live()
                   ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                       $product = Product::query()->find($get('product_id'));
                       if ($product) {
                           $set('price', $product->price);
                           $set('qty', '');
                           $set('total', '');
                       }
                   }),
               Forms\Components\TextInput::make('price')
                   ->prefix('Ks')
                   ->disabled(),
               Forms\Components\Hidden::make('price'),
               Forms\Components\TextInput::make('qty')
                   ->numeric()
                   ->required()
                   ->live()
                   ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                       $price = $get('price');
                       $qty = $get('qty');
                       if ($price && $qty) {
                           $total = $price * $qty;
                           $set('total', $total);
                       }
                   }),
               Forms\Components\TextInput::make('total')
                   ->prefix('Ks')
                   ->disabled(),
               Forms\Components\Hidden::make('total'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                Tables\Columns\TextColumn::make('product.name'),
               Tables\Columns\TextColumn::make('price'),
               Tables\Columns\TextColumn::make('qty'),
               Tables\Columns\TextColumn::make('total'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
