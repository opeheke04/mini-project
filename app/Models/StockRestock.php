<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockRestock extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'product_name',
        'quantity',
        'supplier',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'received_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
