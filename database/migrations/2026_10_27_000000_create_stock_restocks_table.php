<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_restocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->string('supplier')->nullable();
            $table->dateTime('received_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['received_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_restocks');
    }
};
