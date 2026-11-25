<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 미얀마어 배너·공지·카테고리 (미얀마어 화면에서 우선 표시). docs/sql/014-myanmar-content.sql 과 동일. */
class AddMyanmarContent extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('banners', [
            'title_my'    => ['type' => 'TEXT', 'null' => true],
            'subtitle_my' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addColumn('notices', [
            'title_my' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'body_my'  => ['type' => 'MEDIUMTEXT', 'null' => true],
        ]);
        $this->forge->addColumn('categories', ['name_my' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true]]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('banners', ['title_my', 'subtitle_my']);
        $this->forge->dropColumn('notices', ['title_my', 'body_my']);
        $this->forge->dropColumn('categories', 'name_my');
    }
}
