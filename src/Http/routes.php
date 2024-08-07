<?php

use Illuminate\Support\Facades\Route;
use ManoCode\MiniWechat\Http\Controllers;

// 微信授权
Route::resource('wechat_bind', \ManoCode\MiniWechat\Http\Controllers\WechatBindController::class);
// 收货地址
Route::resource('member_address', \ManoCode\Scrm\Http\Controllers\MemberAddresController::class);

Route::get('mini-wechat', [Controllers\MiniWechatController::class, 'index']);
// 获取配置
Route::get('mini-wechat/getConfig/{type}',[Controllers\SettingController::class,'get']);
// 设置配置
Route::post('mini-wechat/saveConfig/{type}',[Controllers\SettingController::class,'set']);

// 搜索用户
Route::any('/mini-wechat/member',[\ManoCode\Scrm\Http\Controllers\MemberAddresController::class,'getMember']);

