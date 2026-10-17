<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminLogModel extends Model
{
    protected $table         = 'admin_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['admin_id', 'admin_name', 'action', 'target_type', 'target_id', 'summary', 'ip', 'created_at'];
}
