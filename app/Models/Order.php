<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'subtotal',
        'tax_amount',
        'total',
        'loyalty_points',
        'status',
    ];

    protected $casts = [
        'subtotal'       => 'float',
        'tax_amount'     => 'float',
        'total'          => 'float',
        'loyalty_points' => 'integer',
    ];
}
