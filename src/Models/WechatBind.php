<?php

namespace ManoCode\MiniWechat\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 微信授权
 */
class WechatBind extends Model
{
    use SoftDeletes;

    protected $table = 'wechat_bind';
    
}
