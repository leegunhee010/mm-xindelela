<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 관리자 담당자 계정(admin_level) + 작업 기록(admin_logs). docs/sql/007-admin-logs.sql 과 동일. */
class AdminStaffAndLogs extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', ['admin_level' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true]]);
        $this->db->table('users')->where('role', 'admin')->update(['admin_level' => 'owner']);

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'admin_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'admin_name'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'action'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'target_type' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'target_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'summary'     => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'ip'          => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('admin_id');
        $this->forge->addKey(['target_type', 'target_id']);
        $this->forge->addKey('created_at');
        $this->forge->createTable('admin_logs', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('admin_logs', true);
        $this->forge->dropColumn('users', 'admin_level');
    }
}
