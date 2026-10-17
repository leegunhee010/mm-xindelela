<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 공급사 홈페이지·제출 자료. docs/sql/005-supplier-docs.sql 과 동일. */
class AddSupplierDocs extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('suppliers', [
            'website'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'doc_files' => ['type' => 'TEXT', 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('suppliers', ['website', 'doc_files']);
    }
}
