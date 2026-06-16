<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                TextInput::make('invoice_number')
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
            ]);
    }
}
