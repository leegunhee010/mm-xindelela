<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * mm.xindelela.com 핵심 테이블.
 * MySQL 5.6 / SQLite3 양쪽에서 동작하도록 Forge 기본 타입만 사용한다.
 */
class CreateCoreTables extends Migration
{
    private function stamps(): array
    {
        return [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ];
    }

    public function up(): void
    {
        // ── 회원 (구매자·공급사·관리자 공용) ──
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'role'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'buyer'], // buyer|supplier|admin
            'email'          => ['type' => 'VARCHAR', 'constraint' => 190],
            'password_hash'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'viber'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'], // pending|approved|rejected|suspended
            'company_name'   => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'business_type'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // online|offline|both
            'fb_page'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'address'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'city'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'doc_files'      => ['type' => 'TEXT', 'null' => true], // JSON [{name,path}]
            'reject_reason'  => ['type' => 'TEXT', 'null' => true],
            'referral_code'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'referred_by'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'points_balance' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'first_order_at' => ['type' => 'DATETIME', 'null' => true],
            'approved_at'    => ['type' => 'DATETIME', 'null' => true],
            'last_login_at'  => ['type' => 'DATETIME', 'null' => true],
            'admin_note'     => ['type' => 'TEXT', 'null' => true],
        ] + $this->stamps());
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('referral_code');
        $this->forge->addKey('status');
        $this->forge->createTable('users');

        // ── 공급사 프로필 ──
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'company_name'    => ['type' => 'VARCHAR', 'constraint' => 190],
            'brand'           => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'intro'           => ['type' => 'TEXT', 'null' => true],
            'logo'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'contact_name'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'phone'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'address'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'biz_no'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'commission_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
        ] + $this->stamps());
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('suppliers');

        // ── 카테고리 ──
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'parent_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'name'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'sort'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('parent_id');
        $this->forge->createTable('categories');

        // ── 상품 ──
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'supplier_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'category_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'code'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'summary'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'description'  => ['type' => 'TEXT', 'null' => true], // HTML
            'thumb'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'images'       => ['type' => 'TEXT', 'null' => true], // JSON
            'price'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0], // 도매가(회원 전용)
            'retail_price' => ['type' => 'INT', 'constraint' => 11, 'null' => true], // 권장소비자가(공개)
            'tiers'        => ['type' => 'TEXT', 'null' => true], // JSON [{min_qty, price}]
            'moq'          => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'unit'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => '개'],
            'stock'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'origin'       => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => '한국'],
            'brand'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'], // draft|pending|active|hidden|rejected
            'reject_reason' => ['type' => 'TEXT', 'null' => true],
            'is_best'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'views'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'order_count'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ] + $this->stamps());
        $this->forge->addKey('id', true);
        $this->forge->addKey('supplier_id');
        $this->forge->addKey('category_id');
        $this->forge->addKey('status');
        $this->forge->createTable('products');

        // ── 장바구니 ──
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty'        => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('carts');

        // ── 주문(신청) ──
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_no'        => ['type' => 'VARCHAR', 'constraint' => 30],
            'user_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            // requested(신청) → quoted(금액확정·입금대기) → paid(입금확인) → preparing(발주) → shipped(배송중) → delivered(완료) / cancelled
            'status'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'requested'],
            'payment_method'  => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'kbzpay'], // kbzpay|cod
            'subtotal'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'shipping_fee'    => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'points_used'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total'           => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'ship_name'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'ship_phone'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'ship_city'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'ship_address'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'memo'            => ['type' => 'TEXT', 'null' => true],
            'admin_memo'      => ['type' => 'TEXT', 'null' => true],
            'payment_proof'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_note'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tracking'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'quoted_at'       => ['type' => 'DATETIME', 'null' => true],
            'proof_at'        => ['type' => 'DATETIME', 'null' => true],
            'paid_at'         => ['type' => 'DATETIME', 'null' => true],
            'shipped_at'      => ['type' => 'DATETIME', 'null' => true],
            'delivered_at'    => ['type' => 'DATETIME', 'null' => true],
            'cancelled_at'    => ['type' => 'DATETIME', 'null' => true],
            'reward_granted'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ] + $this->stamps());
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_no');
        $this->forge->addKey('user_id');
        $this->forge->addKey('status');
        $this->forge->createTable('orders');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'supplier_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'thumb'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'unit_price'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'qty'          => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'line_total'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'item_status'  => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'], // pending|shipped
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addKey('supplier_id');
        $this->forge->createTable('order_items');

        // ── 포인트 원장 ──
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'type'          => ['type' => 'VARCHAR', 'constraint' => 30], // referral_signup|referral_first_order|order_use|order_refund|order_reward|admin_adjust|signup_bonus
            'amount'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'balance_after' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'ref_type'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'ref_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'memo'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('point_logs');

        // ── 공지 ──
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'body'       => ['type' => 'TEXT', 'null' => true],
            'is_pinned'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'views'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ] + $this->stamps());
        $this->forge->addKey('id', true);
        $this->forge->createTable('notices');

        // ── 배너 ──
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'subtitle'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'image'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'link'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'bg'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'sort'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('banners');

        // ── 설정 ──
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'skey'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'svalue'     => ['type' => 'TEXT', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('skey');
        $this->forge->createTable('settings');

        // ── 상품 문의 ──
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'body'       => ['type' => 'TEXT'],
            'answer'     => ['type' => 'TEXT', 'null' => true],
            'answered_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('inquiries');
    }

    public function down(): void
    {
        foreach (['inquiries', 'settings', 'banners', 'notices', 'point_logs', 'order_items', 'orders', 'carts', 'products', 'categories', 'suppliers', 'users'] as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
