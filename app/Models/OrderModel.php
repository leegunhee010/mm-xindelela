<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = ['order_no','user_id','status','payment_method','subtotal','shipping_fee','points_used','total','ship_name','ship_phone','ship_city','ship_address','ship_lat','ship_lng','memo','admin_memo','payment_proof','payment_note','tracking','quoted_at','proof_at','paid_at','shipped_at','delivered_at','cancelled_at','reward_granted'];
}
