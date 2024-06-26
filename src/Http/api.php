<?php

use Illuminate\Support\Facades\Route;
// 小程序相关接口
Route::group(['prefix'=>'/api/mini-app'],function(){
    // 登录接口（无需token）
    Route::post('code-login',[\ManoCode\MiniWechat\Http\Controllers\WechatApiController::class,'codeLogin']);
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
    // 收货地址管理
    Route::group(['prefix'=>'address','middleware'=>\ManoCode\MiniWechat\Http\Middleware\MemberLoginMiddleware::class],function(){
        // 获取列表
        Route::get('list',[\ManoCode\MiniWechat\Http\Controllers\AddressController::class,'list']);
        // 城市列表
        Route::get('tree',[\ManoCode\MiniWechat\Http\Controllers\AddressController::class,'tree']);
        // 设置默认地址
        Route::post('default',[\ManoCode\MiniWechat\Http\Controllers\AddressController::class,'default']);
        // 获取地址信息
        Route::post('detail',[\ManoCode\MiniWechat\Http\Controllers\AddressController::class,'detail']);
        // 创建
        Route::post('create',[\ManoCode\MiniWechat\Http\Controllers\AddressController::class,'create']);
        // 修改
        Route::post('save',[\ManoCode\MiniWechat\Http\Controllers\AddressController::class,'save']);
        // 删除
        Route::post('delete',[\ManoCode\MiniWechat\Http\Controllers\AddressController::class,'delete']);
    });
});

