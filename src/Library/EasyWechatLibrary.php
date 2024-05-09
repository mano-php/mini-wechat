<?php

namespace ManoCode\MiniWechat\Library;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\MiniApp\Application;
use Illuminate\Support\Facades\DB;
use ManoCode\MiniWechat\Models\WechatSetting;

/**
 * 微信扩展包核心库
 */
class EasyWechatLibrary
{
    /**
     * 获取微信小程序 应用
     * @return Application
     * @throws InvalidArgumentException
     */
    public static function getMiniAppApplication(): Application
    {
        
        $config = WechatSetting::query()->where('type','mini-wechat')->whereIn('key',[
            'appid',
            'secret'
        ])->pluck('value','key');

        return new Application([
            'app_id'=>$config['appid'],
            'secret'=>$config['secret']
        ]);
    }

    /**
     * 获取小程序支付应用
     * @return Application|\EasyWeChat\Pay\Application
     * @throws InvalidArgumentException
     */
    public static function getMiniAppPaymentApplication(): Application|\EasyWeChat\Pay\Application
    {
        $paymentConfig = WechatSetting::query()->where('type','wechat-payment')->whereIn('key',[
            'mch_id',
            'certificate',
            'private_key',
            'secret_key'
        ])->pluck('value','key');
        $miniAppConfig = WechatSetting::query()->where('type','mini-wechat')->whereIn('key',[
            'appid',
        ])->pluck('value','key');
        return new \EasyWeChat\Pay\Application([
            'appid'=>$miniAppConfig['appid'],
            'mch_id'=>$paymentConfig['mch_id'],
            'certificate'=>$paymentConfig['certificate'],
            'private_key'=>$paymentConfig['private_key'],
            'secret_key'=>$paymentConfig['secret_key'],
            'http'=>[
                'throw'=>true,
                'timeout'=>20
            ]
        ]);
    }

    /**
     * 获取配置
     * @param string $type
     * @param string $key
     * @param $default
     * @return mixed|string
     */
    public static function getConfig(string $type,string $key,$default=''): mixed
    {
        if(!($config = WechatSetting::query()->where(['type'=>$type,'key'=>$key])->first('value'))){
            return $default;
        }
        return $config->getAttribute('value');
    }
}
