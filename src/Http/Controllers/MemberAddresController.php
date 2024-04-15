<?php

namespace Uupt\MiniWechat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Slowlyo\OwlAdmin\Renderers\Page;
use Slowlyo\OwlAdmin\Renderers\Form;
use Slowlyo\OwlAdmin\Controllers\AdminController;
use Slowlyo\OwlAdmin\Renderers\Tag;
use Uupt\MiniWechat\Models\Member;
use Uupt\MiniWechat\Services\MemberAddresService;

/**
 * 收货地址
 *
 * @property MemberAddresService $service
 */
class MemberAddresController extends AdminController
{
    protected string $serviceName = MemberAddresService::class;
    public function getMember(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Member::query();
        if(strlen(strval($request->input('term')))>=1){
            $query->where(function($where) use($request){
                $where->where('mobile','like',"%{$request->input('term')}%")->orWhere('nickname','like',"%{$request->input('term')}%");
            });
        }
        return response()->json([
            'options'=>$query->select([
                DB::raw('CONCAT(CONCAT(IFNULL(mobile,""),"-"),IFNULL(nickname,"暂无昵称")) as label'),
                DB::raw('id as value'),
            ])->get()
        ]);
    }
    public function list(): Page
    {
        $crud = $this->baseCRUD()
            ->filterTogglable(true)
            ->filter($this->baseFilter()->body([
                amis()->SelectControl('user_id', '用户')->source('/mini-wechat/member')->clearable(),
                amis()->SelectControl('default', '是否默认')->options([['label' => '是','value' => 'yes',],['label' => '否','value' => 'no',],])->clearable()
            ]))
			->headerToolbar([
				$this->createButton(true),
				...$this->baseHeaderToolBar()
			])
            ->columns([
                amis()->TableColumn('id', 'ID')->sortable(),
//                amis()->SelectControl('user_id', '用户')->source('/mini-wechat/member')->disabled(),
                amis()->TableColumn('user_info', '用户')->type('tag'),
				amis()->TableColumn('name', '姓名'),
				amis()->TableColumn('mobile', '手机号'),
				amis()->TableColumn('address', '详细地址'),
				amis()->InputCityControl('city', '城市')->extractValue(false)->allowCity()->allowDistrict()->disabled(),
				amis()->SelectControl('default', '是否默认')->type('tag'),
				amis()->TableColumn('created_at', __('admin.created_at'))->set('type', 'datetime')->sortable(),
				amis()->TableColumn('updated_at', __('admin.updated_at'))->set('type', 'datetime')->sortable(),
                $this->rowActions(true)
            ]);

        return $this->baseList($crud);
    }

    public function form($isEdit = false): Form
    {
        return $this->baseForm()->body([
            amis()->HiddenControl('id', 'ID'),
            amis()->SelectControl('user_id', '用户')->source('/mini-wechat/member')->required(),
			amis()->TextControl('name', '姓名')->required(),
			amis()->TextControl('mobile', '手机号')->required(),
			amis()->TextareaControl('address', '详细地址')->required(),
			amis()->InputCityControl('city', '城市')->extractValue(false)->allowCity()->allowDistrict()->required(),
			amis()->SelectControl('default', '是否默认')->options([['label' => '是','value' => 'yes',],['label' => '否','value' => 'no',],]),
			amis()->HiddenControl('sort', '排序')->value(0),
        ]);
    }

    public function detail(): Form
    {
        return $this->baseDetail()->body([
            amis()->TextControl('id', 'ID')->static(),
            amis()->SelectControl('user_id', '用户')->static('static-select')->source('/mini-wechat/member')->disabled(),
			amis()->TextControl('name', '姓名')->static(),
			amis()->TextControl('mobile', '手机号')->static(),
            amis()->TextareaControl('address', '详细地址')->static()->required(),
			amis()->InputCityControl('city', '城市')->extractValue(false)->allowCity()->allowDistrict()->disabled(),
			amis()->SelectControl('default', '是否默认')->static('static-select')->options([['label' => '是','value' => 'yes',],['label' => '否','value' => 'no',],])->disabled(),
			amis()->TextControl('created_at', __('admin.created_at'))->static(),
			amis()->TextControl('updated_at', __('admin.updated_at'))->static()
        ]);
    }
}
