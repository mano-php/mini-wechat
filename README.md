# 微信小程序用户模块

> 小程序登录
> 
> 绑定手机号
> 
> 微信支付相关功能（业务逻辑内调用）
>

##### 前端授权登录、手机号绑定 相关接口接口文档请参考 [接口文档](https://apifox.com/apidoc/shared-45a3bab4-b170-4365-8cf3-b2d71c9c2068)

#### 微信支付使用（小程序统一支付下单）更多下单参数 请参考 [微信官方文档](https://pay.weixin.qq.com/docs/merchant/apis/mini-program-payment/mini-prepay.html)


```php
$application = \ManoCode\MiniWechat\Library\EasyWechatLibrary::getMiniAppPaymentApplication();
$response = $application->getClient()->postJson('/v3/pay/transactions/jsapi',[
    'appid'=>ManoCode\MiniWechat\Models\WechatSetting::query()->where([
        'type'=>'mini-wechat',
        'key'=>'appid'
    ])->value('value'),
    'mchid'=>\ManoCode\MiniWechat\Models\WechatSetting::query()->where([
        'type'=>'wechat-payment',
        'key'=>'mch_id'
    ])->value('value'),
    'description'=>'demo-test',
    'out_trade_no'=>date('YmdHis').time(),
    'notify_url'=>'http://api.test.wechat.mini-app.com',
    'amount'=>[
        'total'=>1,// 一分钱测试调用(单位为分)
    ],
    'payer'=>[
        'openid'=>\ManoCode\MiniWechat\Models\WechatBind::query()->where(['user_id'=>$this->getMember($request)->getAttribute('id'),'platform'=>'mini-wechat'])->value('openid')
    ]
],[
    'headers'=>[
        'User-Agent'=>'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
        'Accept'=>'application/json'
    ]
]);
dump($response);// 下单结果打印
```

#### 支付处理回调 支付处理回调 [主动查询订单状态官方文档](https://pay.weixin.qq.com/docs/merchant/apis/mini-program-payment/query-by-out-trade-no.html)


```php
$application = \ManoCode\MiniWechat\Library\EasyWechatLibrary::getMiniAppPaymentApplication(); 
$server = $application->getServer();

$server->handlePaid(function (Message $message, \Closure $next) {
    // $message->out_trade_no 获取商户订单号
    // $message->payer['openid'] 获取支付者 openid
    // 🚨🚨🚨 注意：推送信息不一定靠谱哈，请务必验证
    // 建议是拿订单号调用微信支付查询接口，以查询到的订单状态为准
    return $next($message);
});

// 默认返回 ['code' => 'SUCCESS', 'message' => '成功']
return $server->serve();

```
