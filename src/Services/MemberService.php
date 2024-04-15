<?php

namespace Uupt\MiniWechat\Services;

use Uupt\MiniWechat\Models\Member;
use Slowlyo\OwlAdmin\Services\AdminService;
use Uupt\MiniWechat\Models\WechatBind;

/**
 * 用户管理
 *
 * @method Member getModel()
 */
class MemberService extends AdminService
{
    protected string $modelName = Member::class;

    public function query()
    {
        return $this->modelName::query()->orderBy('id', 'DESC');
    }

    public function deleted($ids): void
    {
        // 删除用户时、自动删除微信绑定记录
        WechatBind::query()->whereIn('user_id', explode(',', $ids))->delete();
    }

    /**
     * 列表 获取数据
     *
     * @return array
     */
    public function list()
    {
        $query = $this->listQuery();
        $list = $query->paginate(request()->input('perPage', 20));
        $items = $list->items();
        $total = $list->total();

        foreach ($items as $key => $item) {
            if ($item['status'] === 'enable') {
                $items[$key]['status_title'] = '启用';
            } else {
                $items[$key]['status_title'] = '启用';
            }
            if ($item['sex'] === '1') {
                $items[$key]['sex_title'] = '男';
            } else {
                $items[$key]['sex_title'] = '女';
            }
            if(strlen($item['luck_date'])<=0){
                $items[$key]['luck_date'] = '';
            }
//            $binds = [];
//            foreach (WechatBind::query()->where(['user_id'=>intval($item['id'])])->get() as $wechat){
//                $binds[] = ['mp-wechat'=>'微信公众号','mini-wechat'=>'微信小程序','app-wechat'=>'微信APP','web-wechat'=>'Web端口'][$wechat['platform']];
//            }
//            $items[$key]['binds'] = $binds;
        }
        return compact('items', 'total');
    }
}
