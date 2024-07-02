<?php

namespace ManoCode\MiniWechat\Services;

use ManoCode\Scrm\Models\ScrmUser;
use ManoCode\MiniWechat\Models\Member;
use ManoCode\MiniWechat\Models\WechatBind;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 微信授权
 *
 * @method WechatBind getModel()
 */
class WechatBindService extends AdminService
{
    protected string $modelName = WechatBind::class;

    public function query()
    {
        return $this->modelName::query()->orderBy('id', 'DESC');
    }

    public function list()
    {
        $query = $this->listQuery();

        $list = $query->paginate(request()->input('perPage', 20));
        $items = $list->items();
        $total = $list->total();
        foreach ($items as $key => $item) {
            if ($member = ScrmUser::query()->where(['id' => $item['user_id']])->first()) {
                if (strlen($member->getAttribute('nickname')) >= 1) {
                    $items[$key]['user_info'] = "{$member->getAttribute('nickname')}-{$member->getAttribute('mobile')}";
                } else {
                    $items[$key]['user_info'] = "{$member->getAttribute('mobile')}";
                }
            } else {
                $items[$key]['user_info'] = '';
            }
            $items[$key]['platform'] = ['mp-wechat' => '微信公众号', 'mini-wechat' => '微信小程序', 'app-wechat' => '微信APP', 'web-wechat' => 'Web端口'][$item['platform']];
            if ($item['state'] === 'enable') {
                $items[$key]['status'] = '启用';
            } else {
                $items[$key]['status'] = '禁用';
            }
        }
        return compact('items', 'total');
    }
}
