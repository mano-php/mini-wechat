<?php

use Illuminate\Support\Facades\Route;
// 小程序相关接口
Route::group(['prefix'=>'/api/mini-app'],function(){
    // 登录接口（无需token）
    Route::post('code-login',[\ManoCode\MiniWechat\Http\Controllers\WechatApiController::class,'codeLogin']);
    // 获取配置
    Route::get('get-config',[\ManoCode\MiniWechat\Http\Controllers\WechatApiController::class,'getConfig']);
    // 绑定手机号
    Route::post('bind-mobile',[\ManoCode\MiniWechat\Http\Controllers\WechatApiController::class,'bindMobile']);
    // 上传头像
    Route::post('upload', [\ManoCode\MiniWechat\Http\Controllers\WechatApiController::class,'upload']);
    // 保存用户资料(需要登录)
    Route::post('save-info',[\ManoCode\MiniWechat\Http\Controllers\WechatApiController::class,'saveInfo'])->middleware(\ManoCode\MiniWechat\Http\Middleware\MemberLoginMiddleware::class);
    // 获取用户信息(需要登录)
    Route::post('get-info',[\ManoCode\MiniWechat\Http\Controllers\WechatApiController::class,'getInfo'])->middleware(\ManoCode\MiniWechat\Http\Middleware\MemberLoginMiddleware::class);
    // 拉起支付
    Route::group([
        'prefix'=>'payment',
        'middleware'=>\ManoCode\MiniWechat\Http\Middleware\MemberLoginMiddleware::class
    ],function(){
        // 微信小程序 下单
        Route::post('/mini-wechat-put',[\ManoCode\MiniWechat\Http\Controllers\PaymentController::class,'miniWechatPut']);
        
    });
});

