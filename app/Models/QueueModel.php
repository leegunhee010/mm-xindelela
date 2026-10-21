<?php

namespace App\Models;

use CodeIgniter\Model;

class QueueModel extends Model
{
    protected $table          = 'queues';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $allowedFields  = ['user_id', 'product_id', 'qty', 'status'];
}
