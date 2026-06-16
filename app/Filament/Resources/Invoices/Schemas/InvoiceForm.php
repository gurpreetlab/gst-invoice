<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;


class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->relationship('client', 'name', modifyQueryUsing: fn($query) => $query->where('user_id', Auth::id()))
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('invoice_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'sent' => 'Sent', 'paid' => 'Paid', 'overdue' => 'Overdue'])
                    ->default('draft')
                    ->required(),

                Repeater::make('items')
                    ->label("Invoice Items")
                    ->relationship()
                    ->columnSpan('full')
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name', modifyQueryUsing: fn($query) => $query->where('user_id', Auth::id()))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $product = Product::find($state);

                                if (!$product) {
                                    return;
                                }

                                $set('price', $product->price);
                                $set('tax_rate', $product->tax_rate);

                                $amount = round($product->price + $product->price * $product->tax_rate / 100, 2);
                                $set('amount', $amount);
                            }),

                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $qty = (float) $get('quantity');
                                $price = (float) $get('price');

                                $set('amount', round(($qty * $price) + ($qty * $price) * $get('tax_rate') / 100, 2));
                            }),

                        TextInput::make('price')
                            ->numeric()
                            ->prefix('₹')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $qty = (float) $get('quantity');
                                $price = (float) $get('price');

                                $set('amount', round(($qty * $price) + ($qty * $price) * $get('tax_rate') / 100, 2));
                            }),

                        TextInput::make('tax_rate')
                            ->numeric()
                            ->suffix('%')
                            ->readOnly(),

                        TextInput::make('amount')
                            ->numeric()
                            ->prefix('₹')
                            ->readOnly()
                    ])
                    ->columns(5)
                    ->defaultItems(1)
                    ->addActionLabel('Add Item'),
            ]);
    }
}
