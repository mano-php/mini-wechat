<?php

namespace ManoCode\MiniWechat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Slowlyo\OwlAdmin\Controllers\AdminController;
use ManoCode\MiniWechat\Models\WechatSetting;

/**
 * 设置相关操作
 */
class SettingController extends AdminController
{
    /**
     * 获取配置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function get(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $type = strval($request->route('type'));
        if(strlen($type)<=0){
            return $this->response()->fail("配置类型不存在{$type}");
        }
        return $this->response()->success(WechatSetting::query()->where('type',$type)->pluck('value','key'));
    }

    /**
     * 修改配置
     * @param Request $request
     * @param Response $response
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function set(Request $request,Response $response): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $type = strval($request->route('type'));
        if(strlen($type)<=0){
            return $this->response()->fail("配置类型不存在{$type}");
        }
        foreach ($request->post() as $key=>$item){
            if(!in_array($key,['extension','type','items'])){
                if(!($configItem = WechatSetting::query()->where(['type'=>$type,'key'=>$key])->first())){
                    $configItem = new WechatSetting();
                    $configItem->setAttribute('type',$type);
                    $configItem->setAttribute('key',$key);
                    $configItem->setAttribute('label',$key);
                }
                $configItem->setAttribute('value',$item);
                $configItem->save();
            }
        }
        return $this->response()->success([],'修改成功');
    }
}
