<?php

namespace ManoCode\MiniWechat;

use ManoCode\CustomExtend\Extend\ManoCodeServiceProvider;
use Slowlyo\OwlAdmin\Renderers\Form;
use ManoCode\MiniWechat\Models\WechatSetting;

/**
 * 微信小程序扩展
 */
class MiniWechatServiceProvider extends ManoCodeServiceProvider
{
    public function install()
    {
        parent::install();
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
        try{
            // 获取小程序配置
            $miniWechatConfig = WechatSetting::query()->where('type','mini-wechat')->pluck('value','key');
            // 获取微信支付配置
            $wechatPaymentConfig = WechatSetting::query()->where('type','wechat-payment')->pluck('value','key');
            // 其他配置
            $otherConfig = WechatSetting::query()->where('type','other')->pluck('value','key');
        }catch (\Throwable $exception){
            $miniWechatConfig = collect();
            $wechatPaymentConfig = collect();
            $otherConfig = collect();
        }

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
                        amis()->TextControl('mch_id','商户id')->value($wechatPaymentConfig->get('mch_id','')),
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
                        ])->value($otherConfig->get('force_bind_mobile','')),
                        amis()->TextControl('app_name','应用名称')->required()->value($otherConfig->get('app_name','')),
                        amis()->ImageControl('app_logo','应用logo')->required()->value($otherConfig->get('app_logo','')),
                        amis()->ImageControl('banner_img','活动图')->required()->value($otherConfig->get('banner_img','')),
                        amis()->TextControl('tip_msg','登录页提示语')->required()->value($otherConfig->get('tip_msg','')),
                        amis()->TextControl('protocol','隐私协议地址')->type('input-url')->required()->value($otherConfig->get('protocol','')),
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
