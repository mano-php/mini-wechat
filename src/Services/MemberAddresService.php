<?php

namespace Uupt\MiniWechat\Services;

use Illuminate\Database\Eloquent\Model;
use Uupt\MiniWechat\Models\Member;
use Uupt\MiniWechat\Models\MemberAddres;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 收货地址
 *
 * @method MemberAddres getModel()
 */
class MemberAddresService extends AdminService
{
    protected string $modelName = MemberAddres::class;
    /**
     * saving 钩子 (执行于新增/修改前)
     *
     * 可以通过判断 $primaryKey 是否存在来判断是新增还是修改
     *
     * @param $data
     * @param $primaryKey
     *
     * @return void
     */
    public function saving(&$data, $primaryKey = '')
    {
        $data['city'] = json_encode($data['city']);
    }

    public function query()
    {
        return $this->modelName::query()->orderBy('id','DESC');
    }
    /**
     * saved 钩子 (执行于新增/修改后)
     *
     * 可以通过 $isEdit 来判断是新增还是修改
     *
     * @param Model $model
     * @param $isEdit
     *
     * @return void
     */
    public function saved($model, $isEdit = false)
    {
        if($model->getAttribute('default') =='yes'){
            // 其他地址不默认
            MemberAddres::query()->where('id','<>',$model->getAttribute('id'))->where(['user_id'=>$model->getAttribute('user_id')])->update([
                'default'=>'no'
            ]);
            // 当前为默认
            MemberAddres::query()->where('id',$model->getAttribute('id'))->where(['user_id'=>$model->getAttribute('user_id')])->update([
                'default'=>'yes'
            ]);
        }
    }

    /**
     * 列表 获取数据
     *
     * @return array
     */
    public function list()
    {
        $query = $this->listQuery();
        $list  = $query->paginate(request()->input('perPage', 20));
        $items = $list->items();
        $total = $list->total();
        foreach ($items as $key=>$item){
            if($member = Member::query()->where(['id'=>$item['user_id']])->first()){
                if(strlen($member->getAttribute('nickname'))>=1) {
                    $items[$key]['user_info'] = "{$member->getAttribute('nickname')}-{$member->getAttribute('mobile')}";
                }else{
                    $items[$key]['user_info'] = "{$member->getAttribute('mobile')}";
                }
            }else{
                $items[$key]['user_info'] = '';
            }
            if ($item['default'] === 'yes') {
                $items[$key]['default'] = '是';
            } else {
                $items[$key]['status'] = '否';
            }
        }
        return compact('items', 'total');
    }
}
