<?php

namespace App\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class ReceiptPrinter
{
    public function printSale(Sale $sale): bool
    {
        if (! config('printer.enabled', false)) {
            Log::info('Receipt printing skipped because printer is disabled.', [
                'sale_id' => $sale->id,
                'transaction_number' => $sale->transaction_number,
            ]);

            return false;
        }

        try {
            $printer = new Printer($this->makeConnector());
            $this->writeReceipt($printer, $sale);
            $printer->close();

            return true;
        } catch (\Throwable $e) {
            Log::error('Receipt printer failed to print transaction.', [
                'sale_id' => $sale->id,
                'transaction_number' => $sale->transaction_number,
                'driver' => config('printer.driver'),
                'printer_name' => config('printer.name'),
                'printer_host' => config('printer.host'),
                'printer_port' => config('printer.port'),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    protected function makeConnector()
    {
        if (config('printer.driver') === 'network') {
            return new NetworkPrintConnector(
                config('printer.host', '192.168.1.100'),
                (int) config('printer.port', 9100)
            );
        }

        return new SharedWindowsPrintConnector(config('printer.name', 'POS-58'));
    }

    protected function writeReceipt(Printer $printer, Sale $sale): void
    {
        $sale->loadMissing(['user:id,name', 'items']);
        $separator = "--------------------------------\n";

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text($this->centerText(config('printer.store_name', 'POLIJE MART')) . "\n");
        $printer->setEmphasis(false);
        $printer->text($this->centerText(config('printer.address', 'Jl. Mastrip No. 164, Jember')) . "\n");
        $printer->text($this->centerText('Telp. ' . config('printer.phone', '0812-3456-7890')) . "\n");
        $printer->text($separator);

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text('No   : ' . $sale->transaction_number . "\n");
        $printer->text('Kasir: ' . ($sale->user?->name ?? 'System') . "\n");
        $printer->text('Tgl  : ' . $sale->created_at->format('d-m-Y H:i') . "\n");
        $printer->text('Cust.: ' . ($sale->customer_type ?? 'Umum') . "\n");
        $printer->text($separator);

        foreach ($sale->items as $item) {
            $printer->text($this->trimText($item->product_name, 32) . "\n");
            $printer->text(sprintf('%s x %s', $item->quantity, number_format((float) $item->unit_price, 0, ',', '.')));
            $printer->text(' = ' . number_format((float) $item->line_total, 0, ',', '.') . "\n");
        }

        $printer->text($separator);
        $printer->setEmphasis(true);
        $printer->text('TOTAL : Rp ' . number_format((float) $sale->total, 0, ',', '.') . "\n");
        $printer->setEmphasis(false);
        $printer->text('BAYAR : Rp ' . number_format((float) $sale->amount_paid, 0, ',', '.') . "\n");
        $printer->text('KEMBALI: Rp ' . number_format((float) $sale->change_due, 0, ',', '.') . "\n");
        $printer->text($separator);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($this->centerText(config('printer.footer', 'Terima Kasih Atas Kunjungan Anda')) . "\n");
        $printer->text($this->centerText('Selamat Berbelanja Kembali') . "\n");
        $printer->feed(3);
        $printer->cut();
    }

    protected function centerText(string $text): string
    {
        return $text;
    }

    protected function trimText(string $text, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $maxLength - 1)) . '…';
    }
}
