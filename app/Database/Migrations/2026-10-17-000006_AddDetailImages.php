<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 상품 상세 이미지(번역한 상세 이미지를 세로로 이어 붙임). docs/sql/008-detail-images.sql 과 동일. */
class AddDetailImages extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('products', ['detail_images' => ['type' => 'TEXT', 'null' => true]]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('products', 'detail_images');
    }
}
