<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = ['role','email','password_hash','name','phone','viber','status','company_name','business_type','verify_types','fb_page','address','city','doc_files','reject_reason','referral_code','referred_by','points_balance','first_order_at','approved_at','last_login_at','admin_note'];
}
