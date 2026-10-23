<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Tambahkan baris ini agar kolom title, description, dan status boleh diisi
    protected $fillable = [
        'title',
        'description',
        'status',
    ];
}