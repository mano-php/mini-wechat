<?php

namespace Uupt\MiniWechat\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 用户管理
 */
class Member extends Model
{
    use SoftDeletes;

    protected $table = 'member';
    
}
