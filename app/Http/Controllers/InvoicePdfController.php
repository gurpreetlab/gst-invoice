<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    public function __invoke(Invoice $invoice)
    {
        $invoice->load([
            'client',
            'items.product'
        ]);

        return Pdf::loadView('pdf.invoice', compact('invoice'))->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
