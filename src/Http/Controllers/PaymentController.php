<?php

namespace ManoCode\MiniWechat\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use ManoCode\MiniWechat\Library\EasyWechatLibrary;
use ManoCode\MiniWechat\Models\WechatBind;
use ManoCode\MiniWechat\Models\WechatSetting;
use ManoCode\MiniWechat\Traits\ApiResponseTrait;

class PaymentController
{
    use ApiResponseTrait;

    /**
     * 小程序下单接口
     * @param Request $request
     * @return void
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function miniWechatPut(Request $request)
    {
        $application = EasyWechatLibrary::getMiniAppPaymentApplication();

        $response = $application->getClient()->postJson('/v3/pay/transactions/jsapi',[
            'appid'=>WechatSetting::query()->where([
                'type'=>'mini-wechat',
                'key'=>'appid'
            ])->value('value'),
            'mchid'=>WechatSetting::query()->where([
                'type'=>'wechat-payment',
                'key'=>'mch_id'
            ])->value('value'),
            'description'=>'demo-test',
            'out_trade_no'=>date('YmdHis').time(),
            'notify_url'=>'http://api.test.wechat.uupt.cn.com',
            'amount'=>[
                'total'=>1,// 一分钱测试调用
            ],
            'payer'=>[
                'openid'=>WechatBind::query()->where(['user_id'=>$this->getMember($request)->getAttribute('id'),'platform'=>'mini-wechat'])->value('openid')
            ]
        ],[
            'headers'=>[
                'User-Agent'=>'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'Accept'=>'application/json'
            ]
        ]);
        $this->success('下单成功',[
            'res'=>$response->getContent()
        ]);
    }
}
