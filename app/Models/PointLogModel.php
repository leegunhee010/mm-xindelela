<?php

namespace App\Models;

use CodeIgniter\Model;

class PointLogModel extends Model
{
    protected $table            = 'point_logs';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $updatedField     = '';
    protected $allowedFields    = ['user_id','type','amount','balance_after','ref_type','ref_id','memo'];
}
