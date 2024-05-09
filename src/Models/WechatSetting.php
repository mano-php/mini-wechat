<?php

namespace ManoCode\MiniWechat\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 微信相关设置
 */
class WechatSetting extends Model
{
    use SoftDeletes;

    protected $table = 'wechat_setting';

}
