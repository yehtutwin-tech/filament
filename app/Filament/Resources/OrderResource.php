<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Customer;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                   ->label('Customer')
                   ->options(Customer::query()->pluck('name', 'id'))
                   ->searchable()
                   ->preload()
                   ->required()
                   ->live()
                   ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        $use_customer_info = $get('use_customer_info');
                        if ($use_customer_info) {
                            $customer = Customer::query()->find($get('customer_id'));
                            if ($customer) {
                                $set('name', $customer->name);
                                $set('phone', $customer->phone);
                            }
                        } else {
                            $set('name', '');
                            $set('phone', '');
                        }
                   }),
               Forms\Components\Checkbox::make('use_customer_info')
                   ->inline(false)
                   ->label('Use customer info as order info')
                   ->live()
                   ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        $use_customer_info = $get('use_customer_info');
                        if ($use_customer_info) {
                            $customer = Customer::query()->find($get('customer_id'));
                            if ($customer) {
                                $set('name', $customer->name);
                                $set('phone', $customer->phone);
                            }
                        } else {
                            $set('name', '');
                            $set('phone', '');
                        }
                   }),
               Forms\Components\TextInput::make('name')
                   ->label('Customer Name')
                   ->maxLength(255)
                   ->required(),
               Forms\Components\TextInput::make('phone')
                   ->label('Phone number')
                   ->tel()
                   ->required(),
               Forms\Components\Textarea::make('billing_address')
                   ->maxLength(65535)
                   ->columnSpan('full'),
               Forms\Components\Textarea::make('delivery_address')
                   ->maxLength(65535)
                   ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                   ->label('Order Id')
                   ->searchable()
                   ->sortable(),
               Tables\Columns\TextColumn::make('customer.name')
                   ->searchable()
                   ->sortable(),
               Tables\Columns\TextColumn::make('customer.phone')
                   ->searchable()
                   ->sortable(),
               Tables\Columns\TextColumn::make('created_at')
                   ->datetime()
                   ->searchable()
                   ->sortable(),
            ])
            ->filters([
                //
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
            RelationManagers\OrderdetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
