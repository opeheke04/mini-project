<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
        });

        $defaults = [
            'store_name' => 'POLIJE MART',
            'address' => 'Jl. Mastrip No. 164, Jember',
            'phone' => '0812-3456-7890',
            'footer' => 'Terima Kasih Atas Kunjungan Anda',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('store_settings')->updateOrInsert(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
