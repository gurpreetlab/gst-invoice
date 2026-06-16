<p>Hello {{ $invoice->client->name }},</p>

<p>
Please find attached Invoice
<strong>{{ $invoice->invoice_number }}</strong>.
</p>

<p>
Amount Due:
₹{{ number_format($invoice->grand_total, 2) }}
</p>

<p>Thank you.</p>
