<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'company',
        'email',
        'phone',
        'message',
        'status',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
