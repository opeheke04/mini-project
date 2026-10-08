<?php

return [
    'enabled' => env('PRINTER_ENABLED', false),
    'driver' => env('ESC_POS_DRIVER', 'windows'),
    'name' => env('ESC_POS_PRINTER_NAME', 'POS-58'),
    'host' => env('ESC_POS_PRINTER_HOST', '192.168.1.100'),
    'port' => (int) env('ESC_POS_PRINTER_PORT', 9100),
    'store_name' => env('STORE_NAME', 'POLIJE MART'),
    'address' => env('STORE_ADDRESS', 'Jl. Mastrip No. 164, Jember'),
    'phone' => env('STORE_PHONE', '0812-3456-7890'),
    'footer' => env('STORE_FOOTER', 'Terima Kasih Atas Kunjungan Anda'),
];
