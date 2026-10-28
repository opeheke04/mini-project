<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 22px;
        }

        .header p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .total-row td,
        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>No: {{ $sale->transaction_number }}</p>
        <p>Tanggal: {{ $sale->created_at->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3">Grand Total:</td>
                <td>Rp {{ number_format((float) $sale->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
