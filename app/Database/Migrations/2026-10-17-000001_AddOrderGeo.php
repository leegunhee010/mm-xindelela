<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 주문 배송지 좌표 (구글맵 주소 검색). docs/sql/003-order-geo.sql 과 동일. */
class AddOrderGeo extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('orders', [
            'ship_lat' => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'ship_lng' => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('orders', ['ship_lat', 'ship_lng']);
    }
}
