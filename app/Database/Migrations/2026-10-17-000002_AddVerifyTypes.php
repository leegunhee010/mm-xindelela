<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 사업자 인증 유형(오프라인 매장·온라인 판매·판매 네트워크). docs/sql/004-verify-types.sql 과 동일. */
class AddVerifyTypes extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'verify_types' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'verify_types');
    }
}
