<?php

namespace ManoCode\MiniWechat\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 收货地址
 */
class MemberAddres extends Model
{
    use SoftDeletes;

    protected $table = 'member_address';
    
}
