<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $sale->transaction_number }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 2mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            width: 54mm;
            margin: 0 auto;
            color: #000;
            font: 11px/1.35 "Courier New", monospace;
        }

        .center {
            text-align: center;
        }

        .store-name {
            font-size: 14px;
            font-weight: bold;
        }

        .separator {
            border: 0;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .meta-row,
        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 4px;
        }

        .meta-label {
            flex: 0 0 34px;
        }

        .meta-value {
            flex: 1;
            overflow-wrap: anywhere;
        }

        .item {
            margin: 5px 0;
            overflow-wrap: anywhere;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            gap: 4px;
        }

        .grand-total {
            font-size: 13px;
            font-weight: bold;
        }

        @media screen {
            body {
                margin: 20px auto;
                padding: 8px;
                box-shadow: 0 0 8px #999;
            }
        }
    </style>
</head>
<body>
    <header class="center">
        <div class="store-name">{{ config('printer.store_name', 'POLIJE MART') }}</div>
        <div>{{ config('printer.address', 'Jl. Mastrip No. 164, Jember') }}</div>
        <div>Telp. {{ config('printer.phone', '0812-3456-7890') }}</div>
    </header>

    <hr class="separator">
    <div class="meta-row"><span class="meta-label">No</span><span class="meta-value">: {{ $sale->transaction_number }}</span></div>
    <div class="meta-row"><span class="meta-label">Kasir</span><span class="meta-value">: {{ $sale->user?->name ?? 'Akun dihapus' }}</span></div>
    <div class="meta-row"><span class="meta-label">Tgl</span><span class="meta-value">: {{ $sale->created_at->format('d-m-Y H:i') }}</span></div>
    <div class="meta-row"><span class="meta-label">Pelanggan</span><span class="meta-value">: {{ $sale->customer_type ?: 'Umum' }}</span></div>

    <hr class="separator">
    @foreach ($sale->items as $item)
        <div class="item">
            <div>{{ $item->product_name }}</div>
            <div class="item-detail">
                <span>{{ $item->quantity }} x {{ number_format((float) $item->unit_price, 0, ',', '.') }}</span>
                <span>{{ number_format((float) $item->line_total, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach

    <hr class="separator">
    <div class="total-row"><span>Subtotal</span><span>{{ number_format((float) $sale->subtotal, 0, ',', '.') }}</span></div>
    @if ((float) $sale->discount_percent > 0 || (float) $sale->discount_amount > 0)
        <div class="total-row"><span>Diskon</span><span>-{{ number_format(((float) $sale->subtotal * (float) $sale->discount_percent / 100) + (float) $sale->discount_amount, 0, ',', '.') }}</span></div>
    @endif
    @if ((float) $sale->tax > 0)
        <div class="total-row"><span>Pajak/PPN</span><span>{{ number_format((float) $sale->tax, 0, ',', '.') }}</span></div>
    @endif
    @if ((float) $sale->other_fee > 0)
        <div class="total-row"><span>Biaya lain</span><span>{{ number_format((float) $sale->other_fee, 0, ',', '.') }}</span></div>
    @endif
    <div class="total-row grand-total"><span>TOTAL</span><span>{{ number_format((float) $sale->total, 0, ',', '.') }}</span></div>
    <div class="total-row"><span>BAYAR</span><span>{{ number_format((float) $sale->amount_paid, 0, ',', '.') }}</span></div>
    <div class="total-row"><span>KEMBALI</span><span>{{ number_format((float) $sale->change_due, 0, ',', '.') }}</span></div>

    <hr class="separator">
    <div class="center">
        <div>{{ config('printer.footer', 'Terima Kasih Atas Kunjungan Anda') }}</div>
        <div>Selamat Berbelanja Kembali</div>
    </div>

    <script>
        window.addEventListener('load', () => {
            window.setTimeout(() => window.print(), 250);
        });
    </script>
</body>
</html>
