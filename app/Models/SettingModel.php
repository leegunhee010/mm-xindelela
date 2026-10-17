<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = ['skey','svalue'];
    protected $createdField     = ''; // settings 테이블엔 updated_at 만 있음 (새 키 insert 시 오류 나던 문제)
}
