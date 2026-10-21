<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 줄서기(queues) + 사이트 안 알림(notifications). docs/sql/012-queues-notifications.sql 과 동일.
 * 상품 status 에 'coming'(입고 예정) 추가 — VARCHAR 라 컬럼 변경 없음.
 */
class AddQueuesAndNotifications extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'waiting'], // waiting|cancelled|arrived|failed
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'product_id']);
        $this->forge->addKey(['product_id', 'status']);
        $this->forge->createTable('queues', true);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'type'       => ['type' => 'VARCHAR', 'constraint' => 30],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'body'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'link'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'read_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'read_at']);
        $this->forge->createTable('notifications', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('queues', true);
    }
}
