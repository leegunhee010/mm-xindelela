<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 출고 상세(배송 방법·배송사·송장·발송일·예상 도착일·메모) JSON. docs/sql/006-order-ship-info.sql 과 동일. */
class AddOrderShipInfo extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('orders', ['ship_info' => ['type' => 'TEXT', 'null' => true]]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('orders', 'ship_info');
    }
}
