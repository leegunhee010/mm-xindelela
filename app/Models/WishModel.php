<?php

namespace App\Models;

use CodeIgniter\Model;

class WishModel extends Model
{
    protected $table          = 'wishes';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $updatedField   = '';
    protected $allowedFields  = ['user_id', 'product_id'];
}
