<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background: #f5f5f5;
        }

        .text-right {
            text-align: right;
        }

        .mb-20 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h1>Invoice #{{ $invoice->invoice_number }}</h1>

<div class="mb-20">
    <strong>Client:</strong> {{ $invoice->client->name }} <br>
    <strong>Email:</strong> {{ $invoice->client->email }} <br>
    <strong>Date:</strong> {{ $invoice->invoice_date }} <br>
    <strong>Due:</strong> {{ $invoice->due_date }}
</div>

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Tax %</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>{{ $item->tax_rate }}%</td>
                <td>₹{{ number_format($item->amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3 class="text-right">
    Total: ₹{{ number_format($invoice->items->sum('amount'), 2) }}
</h3>

</body>
</html>
