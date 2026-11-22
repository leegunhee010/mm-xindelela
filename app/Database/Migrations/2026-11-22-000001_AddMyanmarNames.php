<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 미얀마어 상품명·공급사명 (미얀마어 화면에서 우선 표시). docs/sql/013-myanmar-names.sql 과 동일. */
class AddMyanmarNames extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('products', ['name_my' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]]);
        $this->forge->addColumn('suppliers', ['name_my' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true]]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('products', 'name_my');
        $this->forge->dropColumn('suppliers', 'name_my');
    }
}
