<?php

namespace ManoCode\MiniWechat\Http\Controllers;

use Slowlyo\OwlAdmin\Renderers\Page;
use Slowlyo\OwlAdmin\Renderers\Form;
use Slowlyo\OwlAdmin\Controllers\AdminController;
use ManoCode\MiniWechat\Services\MemberService;

/**
 * 用户管理
 *
 */
class MemberController extends AdminController
{
    protected string $serviceName = MemberService::class;

    public function list(): Page
    {
        $crud = $this->baseCRUD()
            ->filterTogglable(true)
            ->filter($this->baseFilter()->body([
                amis()->SelectControl('user_id', '用户')->source('/mini-wechat/member')->clearable(),
                amis()->SelectControl('status', '状态')->options([['label' => '启用','value' => 'enable',],['label' => '禁用','value' => 'disable',],])->clearable(),
                amis()->SelectControl('sex', '性别')->options([['label' => '男','value' => '1',],['label' => '女','value' => '2',],])->clearable(),
            ]))
			->headerToolbar([
				$this->createButton(true),
				...$this->baseHeaderToolBar()
			])
            ->columns([
                amis()->TableColumn('id', 'ID')->sortable(),
				amis()->TableColumn('nickname', '昵称'),
				amis()->TableColumn('avatar', '头像')->type('avatar')->src('${avatar}'),
				amis()->TableColumn('mobile', '手机号'),
//				amis()->TableColumn('binds', '绑定')->type('tags'),
                admin_corp_amis()->employeeForm('leader_ids', '负责人')->static()->placeholder('未分配'),
				amis()->SelectControl('status_title', '状态')->type('tag'),
				amis()->SelectControl('sex_title', '性别')->type('tag'),
				amis()->DateControl('luck_date', '幸运日')->type('tag')->placeholder('暂未设置'),
				amis()->TableColumn('created_at', __('admin.created_at'))->set('type', 'datetime')->sortable(),
				amis()->TableColumn('updated_at', __('admin.updated_at'))->set('type', 'datetime')->sortable(),
                $this->rowActions(true)
            ]);

        return $this->baseList($crud);
    }

    public function form($isEdit = false): Form
    {
        return $this->baseForm()->body([
            amis()->HiddenControl('id'),
            amis()->TextControl('nickname', '昵称'),
            amis()->ImageControl('avatar', '头像'),
            amis()->TextControl('mobile', '手机号'),
            admin_corp_amis()->employeeForm('leader_ids', '负责人'),
            amis()->SelectControl('status', '状态')->options([['label' => '启用','value' => 'enable',],['label' => '禁用','value' => 'disable',],]),
            amis()->SelectControl('sex', '性别')->options([['label' => '男','value' => '1',],['label' => '女','value' => '2',],]),
            amis()->DateControl('luck_date', '幸运日')->format('Y-MM-DD'),
        ]);
    }

    public function detail(): Form
    {
        return $this->baseDetail()->body([
            amis()->TextControl('id', 'ID')->static(),
            amis()->TextControl('nickname', '昵称')->static(),
            amis()->ImageControl('avatar', '头像')->type('static-avatar')->src('${avatar}')->static(),
            amis()->TextControl('mobile', '手机号')->static(),
            admin_corp_amis()->employeeForm('leader_ids', '负责人')->static(),
            amis()->SelectControl('status', '状态')->static('static-select')->options([['label' => '启用','value' => 'enable',],['label' => '禁用','value' => 'disable',],])->disabled(),
            amis()->SelectControl('sex', '性别')->static('static-select')->options([['label' => '男','value' => '1',],['label' => '女','value' => '2',],])->disabled(),
            amis()->DateControl('luck_date', '幸运日')->static('static-date')->placeholder('暂未设置')->disabled(),
            amis()->TextControl('created_at', __('admin.created_at'))->static(),
            amis()->TextControl('updated_at', __('admin.updated_at'))->static()
        ]);
    }
}
