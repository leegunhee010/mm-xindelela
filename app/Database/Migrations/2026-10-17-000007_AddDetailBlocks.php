<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 상품 상세 블록(문단·이미지·이미지 조각·영상, 메이크노브 등록 방식). docs/sql/009-detail-blocks.sql 과 동일. */
class AddDetailBlocks extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('products', ['detail' => ['type' => 'MEDIUMTEXT', 'null' => true]]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('products', 'detail');
    }
}
