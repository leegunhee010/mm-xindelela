<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** 관심상품(wishes) + 상품 판매 주의사항(products.sale_note). docs/sql/011-wishes-sale-note.sql 과 동일. */
class AddWishesAndSaleNote extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('products', ['sale_note' => ['type' => 'TEXT', 'null' => true]]);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'product_id']);
        $this->forge->addKey('product_id');
        $this->forge->createTable('wishes', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('wishes', true);
        $this->forge->dropColumn('products', 'sale_note');
    }
}
