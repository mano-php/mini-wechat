<?php

namespace Uupt\MiniWechat\Http\Controllers;

use Slowlyo\OwlAdmin\Renderers\Page;
use Slowlyo\OwlAdmin\Renderers\Form;
use Slowlyo\OwlAdmin\Controllers\AdminController;
use Uupt\MiniWechat\Services\WechatBindService;

/**
 * 微信授权
 *
 * @property WechatBindService $service
 */
class WechatBindController extends AdminController
{
    protected string $serviceName = WechatBindService::class;

    public function list(): Page
    {
        $crud = $this->baseCRUD()
            ->filterTogglable(true)
			->headerToolbar([
//				$this->createButton(true),
//				...$this->baseHeaderToolBar()
			])
            ->filter($this->baseFilter()->body([
                amis()->SelectControl('user_id', '用户')->source('/mini-wechat/member')->clearable(),
                amis()->SelectControl('platform', '授权平台')->options([['label' => '微信公众号','value' => 'mp-wechat',],['label' => '微信小程序','value' => 'mini-wechat',],['label' => 'APP','value' => 'app-wechat',],['label' => 'PC端','value' => 'web-wechat',],]),
                amis()->SelectControl('status', '状态')->options([['label' => '启用','value' => 'enable',],['label' => '禁用','value' => 'disable',],]),
            ]))
            ->columns([
                amis()->TableColumn('id', 'ID')->sortable(),
//                amis()->SelectControl('user_id', '用户')->source('/mini-wechat/member')->disabled(),
                amis()->TableColumn('user_info', '用户')->type('tag'),
				amis()->TableColumn('unionid', '平台ID'),
				amis()->TableColumn('openid', '开放ID'),
				amis()->SelectControl('platform', '授权平台')->type('tag'),
				amis()->SelectControl('status', '状态')->type('tag'),
				amis()->TableColumn('created_at', __('admin.created_at'))->set('type', 'datetime')->sortable(),
				amis()->TableColumn('updated_at', __('admin.updated_at'))->set('type', 'datetime')->sortable(),
//                $this->rowActions(true)
            ]);

        return $this->baseList($crud);
    }

    public function form($isEdit = false): Form
    {
        return $this->baseForm()->body([
            amis()->HiddenControl('id'),
            amis()->SelectControl('user_id', '用户')->source('/mini-wechat/member'),
			amis()->TextControl('unionid', '平台ID'),
			amis()->TextControl('openid', '开放ID'),
			amis()->SelectControl('platform', '授权平台')->options([['label' => '微信公众号','value' => 'mp-wechat',],['label' => '微信小程序','value' => 'mini-wechat',],['label' => 'APP','value' => 'app-wechat',],['label' => 'PC端','value' => 'web-wechat',],]),
			amis()->SelectControl('status', '状态')->options([['label' => '启用','value' => 'enable',],['label' => '禁用','value' => 'disable',],]),
        ]);
    }

    public function detail(): Form
    {
        return $this->baseDetail()->body([
            amis()->TextControl('id', 'ID')->static(),
            amis()->SelectControl('user_id', '用户')->source('/mini-wechat/member')->disabled(),
			amis()->TextControl('unionid', '平台ID')->static(),
			amis()->TextControl('openid', '开放ID')->static(),
			amis()->SelectControl('platform', '授权平台')->options([['label' => '微信公众号','value' => 'mp-wechat',],['label' => '微信小程序','value' => 'mini-wechat',],['label' => 'APP','value' => 'app-wechat',],['label' => 'PC端','value' => 'web-wechat',],])->disabled(),
			amis()->SelectControl('status', '状态')->options([['label' => '启用','value' => 'enable',],['label' => '禁用','value' => 'disable',],])->disabled(),
			amis()->TextControl('created_at', __('admin.created_at'))->static(),
			amis()->TextControl('updated_at', __('admin.updated_at'))->static()
        ]);
    }
}
