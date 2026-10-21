<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table          = 'notifications';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $updatedField   = '';
    protected $allowedFields  = ['user_id', 'type', 'title', 'body', 'link', 'read_at'];
}
