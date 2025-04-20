<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
{
    $statuses = [
        ['name' => 'Nouvelle', 'color_code' => '#3498db', 'order' => 1],
        ['name' => 'En traitement', 'color_code' => '#f39c12', 'order' => 2],
        ['name' => 'En production', 'color_code' => '#9b59b6', 'order' => 3],
        ['name' => 'Expédié', 'color_code' => '#2ecc71', 'order' => 4],
        ['name' => 'Complété', 'color_code' => '#27ae60', 'order' => 5],
        ['name' => 'Annulé', 'color_code' => '#e74c3c', 'order' => 6],
    ];

    foreach ($statuses as $status) {
        DB::table('order_status')->insert($status);
    }
}
}
