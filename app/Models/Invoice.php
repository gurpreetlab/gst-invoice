<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'notes',
        'status',
    ];


    protected static function booted(): void
    {
        static::creating(function ($invoice) {
            $year = now()->year;

            $lastInvoice = static::whereYear('created_at', $year)
                ->latest('id')
                ->first();

            $nextNumber = 1;

            if ($lastInvoice) {
                $parts = explode('-', $lastInvoice->invoice_number);
                $nextNumber = (int) end($parts) + 1;
            }

            $invoice->invoice_number = 'INV-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
