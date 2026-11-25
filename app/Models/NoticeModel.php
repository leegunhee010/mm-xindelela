<?php

namespace App\Models;

use CodeIgniter\Model;

class NoticeModel extends Model
{
    protected $table            = 'notices';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = ['title','title_my','body','body_my','is_pinned','views'];
}
