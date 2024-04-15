<?php

use Illuminate\Support\Facades\Route;
//date_default_timezone_set('Asia/Shanghai');
Route::any('/clear',function (\Illuminate\Http\Response $response){
   \Uupt\MiniWechat\Models\Member::query()->delete();
   \Uupt\MiniWechat\Models\WechatBind::query()->delete();
   \Uupt\MiniWechat\Models\MemberAddres::query()->delete();
   return response()->json([
       'status'=>'success'
   ]);
});
// 小程序相关接口
Route::group(['prefix'=>'/api/mini-app'],function(){
    // 登录接口（无需token）
    Route::post('code-login',[\Uupt\MiniWechat\Http\Controllers\WechatApiController::class,'codeLogin']);
    // 绑定手机号
    Route::post('bind-mobile',[\Uupt\MiniWechat\Http\Controllers\WechatApiController::class,'bindMobile']);
    // 上传头像
    Route::post('upload', [\Uupt\MiniWechat\Http\Controllers\WechatApiController::class,'upload']);
    // 保存用户资料(需要登录)
    Route::post('save-info',[\Uupt\MiniWechat\Http\Controllers\WechatApiController::class,'saveInfo'])->middleware(\Uupt\MiniWechat\Http\Middleware\MemberLoginMiddleware::class);
    // 获取用户信息(需要登录)
    Route::post('get-info',[\Uupt\MiniWechat\Http\Controllers\WechatApiController::class,'getInfo'])->middleware(\Uupt\MiniWechat\Http\Middleware\MemberLoginMiddleware::class);
    // 拉起支付
    Route::group([
        'prefix'=>'payment',
        'middleware'=>\Uupt\MiniWechat\Http\Middleware\MemberLoginMiddleware::class
    ],function(){
        // 微信小程序 下单
        Route::post('/mini-wechat-put',[\Uupt\MiniWechat\Http\Controllers\PaymentController::class,'miniWechatPut']);
    });
    // 收货地址管理
    Route::group(['prefix'=>'address','middleware'=>\Uupt\MiniWechat\Http\Middleware\MemberLoginMiddleware::class],function(){
        // 获取列表
        Route::get('list',[\Uupt\MiniWechat\Http\Controllers\AddressController::class,'list']);
        // 城市列表
        Route::get('tree',[\Uupt\MiniWechat\Http\Controllers\AddressController::class,'tree']);
        // 设置默认地址
        Route::post('default',[\Uupt\MiniWechat\Http\Controllers\AddressController::class,'default']);
        // 获取地址信息
        Route::post('detail',[\Uupt\MiniWechat\Http\Controllers\AddressController::class,'detail']);
        // 创建
        Route::post('create',[\Uupt\MiniWechat\Http\Controllers\AddressController::class,'create']);
        // 修改
        Route::post('save',[\Uupt\MiniWechat\Http\Controllers\AddressController::class,'save']);
        // 删除
        Route::post('delete',[\Uupt\MiniWechat\Http\Controllers\AddressController::class,'delete']);
    });
});

