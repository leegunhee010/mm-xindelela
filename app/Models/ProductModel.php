<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = ['supplier_id','category_id','code','name','summary','description','detail','detail_images','thumb','images','price','retail_price','tiers','moq','unit','stock','origin','brand','status','reject_reason','is_best','views','order_count'];
}
