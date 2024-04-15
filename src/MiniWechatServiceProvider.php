<?php

namespace Uupt\MiniWechat;

use Slowlyo\OwlAdmin\Models\AdminMenu;
use Slowlyo\OwlAdmin\Renderers\Form;
use Slowlyo\OwlAdmin\Renderers\TextControl;
use Slowlyo\OwlAdmin\Extend\ServiceProvider;
use Uupt\MiniWechat\Models\WechatSetting;

class MiniWechatServiceProvider extends ServiceProvider
{
    public function install()
    {
        parent::install();
        $user_node_id = AdminMenu::query()->insertGetId([
            'parent_id' => 0,
            'order' => 0,
            'title' => '用户模块',
            'icon' => 'ph:user-gear',
            'url' => '/users',
            'url_type' => 1,
            'visible' => 1,
            'is_home' => 0,
            'keep_alive' => 0,
            'iframe_url' => NULL,
            'component' => 'amis',
            'is_full' => 0,
            'extension' => NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        AdminMenu::query()->insert([
            [
                'parent_id' => $user_node_id,
                'order' => 8,
                'title' => '用户管理',
                'icon' => 'ph:user-gear',
                'url' => '/member',
                'url_type' => 1,
                'visible' => 1,
                'is_home' => 0,
                'keep_alive' => 0,
                'iframe_url' => NULL,
                'component' => NULL,
                'is_full' => 0,
                'extension' => NULL,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'parent_id' => $user_node_id,
                'order' => 8,
                'title' => '微信授权',
                'icon' => 'ph:user-gear',
                'url' => '/wechat_bind1',
                'url_type' => 1,
                'visible' => 0,
                'is_home' => 0,
                'keep_alive' => 0,
                'iframe_url' => NULL,
                'component' => NULL,
                'is_full' => 0,
                'extension' => NULL,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
    public function register()
    {
        $this->loadRoute();
    }
    public function loadRoute()
    {
        include_once(__DIR__.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'api.php');
    }
	public function settingForm()
	{
        // 获取小程序配置
        $miniWechatConfig = WechatSetting::query()->where('type','mini-wechat')->pluck('value','key');
        // 获取微信支付配置
        $wechatPaymentConfig = WechatSetting::query()->where('type','wechat-payment')->pluck('value','key');
        // 其他配置
        $otherConfig = WechatSetting::query()->where('type','other')->pluck('value','key');


	    return $this->basePage()->body([
            amis()->Tabs()->tabs([
                [
                    'title'=>'小程序配置',
                    'body'=>$this->baseSettingForm()->body([
                        amis()->HiddenControl('type','配置类型')->value('mini-wechat'),
                        amis()->TextControl('appid','应用ID')->value($miniWechatConfig->get('appid','')),
                        amis()->TextControl('secret','秘钥')->value($miniWechatConfig->get('secret','')),
//                        amis()->TextControl('token','token')->value($miniWechatConfig->get('token','')),
//                        amis()->TextControl('aes_key','aes_key')->value($miniWechatConfig->get('aes_key','')),
                    ])->data(['extension' => $this->getName()])
                        ->initApi([
                            'url'    => admin_url('mini-wechat/getConfig/mini-wechat'),
                            'method' => 'GET',
                            'data'   => [
                                'extension' => $this->getName(),
                            ],
                        ])
                        ->actions([
                            amis('submit')->label('保存')->level('primary'),
                            amis('reset')->label('重置')->level('primary'),
                        ])
                        ->api('post:' . admin_url('mini-wechat/saveConfig/mini-wechat'))
                ],
                [
                    'title'=>'支付配置',
                    'body'=>$this->baseSettingForm()->body([
                        amis()->HiddenControl('type','配置类型')->value('wechat-payment'),
                        amis()->TextControl('mch_id','商户ID')->value($wechatPaymentConfig->get('mch_id','')),
                        amis()->TextControl('secret_key','v3 API 秘钥')->value($wechatPaymentConfig->get('secret_key','')),
                        amis()->TextareaControl('certificate','证书')->value($wechatPaymentConfig->get('certificate','')),
                        amis()->TextareaControl('private_key','证书秘钥')->value($wechatPaymentConfig->get('private_key','')),
//                        amis()->TextControl('v2_secret_key','v2 API 秘钥')->value($wechatPaymentConfig->get('v2_secret_key','')),
                    ])->data(['extension' => $this->getName()])
                        ->initApi([
                            'url'    => admin_url('mini-wechat/getConfig/wechat-payment'),
                            'method' => 'GET',
                            'data'   => [],
                        ])
                        ->actions([
                            amis('submit')->label('保存')->level('primary'),
                            amis('reset')->label('重置')->level('primary'),
                        ])
                        ->api('post:' . admin_url('mini-wechat/saveConfig/wechat-payment'))
                ],
                [
                    'title'=>'其他配置',
                    'body'=>$this->baseSettingForm()->body([
                        amis()->HiddenControl('type','配置类型')->value('other'),
                        amis()->SelectControl('force_bind_mobile','是否强制绑定手机号')->options([
                            [
                                'label'=>'否',
                                'value'=>'no'
                            ],
                            [
                                'label'=>'是',
                                'value'=>'yes'
                            ],
                        ])->value($otherConfig->get('force_bind_mobile',''))
                    ])->data(['extension' => $this->getName()])
                        ->initApi([
                            'url'    => admin_url('mini-wechat/getConfig/other'),
                            'method' => 'GET',
                            'data'   => [],
                        ])
                        ->actions([
                            amis('submit')->label('保存')->level('primary'),
                            amis('reset')->label('重置')->level('primary'),
                        ])
                        ->api('post:' . admin_url('mini-wechat/saveConfig/other'))
                ],
            ])
	    ]);
	}
    protected function basePage()
    {
        return Form::make()
            ->panelClassName('border-0')
            ->affixFooter()
            ->title('')->actions([]);
    }
}
