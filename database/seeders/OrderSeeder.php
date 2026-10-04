<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // One deterministic test order for benchmarking.
        // ID will be 1 (first row in a fresh DB).
        Order::create([
            'customer_name'  => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'subtotal'       => 100.00,
            'tax_amount'     => 0,
            'total'          => 0,
            'loyalty_points' => 0,
            'status'         => 'pending',
        ]);
    }
}
