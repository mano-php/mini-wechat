<?php

namespace ManoCode\MiniWechat\Http\Controllers;

use Illuminate\Http\Request;
use ManoCode\MiniWechat\Models\MemberAddres;
use ManoCode\MiniWechat\Traits\ApiResponseTrait;

/**
 * 用户收货地址管理
 */
class AddressController
{
    use ApiResponseTrait;
    public function tree(): \Illuminate\Http\JsonResponse
    {
        return $this->success('获取成功',json_decode(file_get_contents(__DIR__.'/../../country_tree.json'),true)['children']);
    }
    public function list(Request $request): \Illuminate\Http\JsonResponse
    {
        $list = MemberAddres::query()->where(['user_id'=>$this->getMember($request)->getAttribute('id')])->get();
        foreach ($list as $key=>$item){
            $list[$key]['city'] = json_decode($item['city'],true);
            $list[$key]['default'] = $item['default']==='yes'?true:false;
        }
        return $this->success('获取成功',[
            'list'=>$list
        ]);
    }
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $post = $request->only([
            'name',
            'mobile',
            'address',
            'city',
            'default',
            'sort',
        ]);
        if(!is_string($post['city'])){
            $post['city'] = json_encode($post['city']);
        }
        if(isset($post['default']) && $post['default']){
            $post['default'] = 'yes';
        }else{
            $post['default'] = 'no';
        }
        if(isset($post['sort']) && $post['sort']>=1){
            $post['sort'] = intval($post['sort']);
        }else{
            $post['sort'] = 0;
        }
        if(!(isset($post['name']) && strlen($post['name'])>=1)){
            return $this->fail('收货人姓名不能为空');
        }
        if(!(isset($post['mobile']) && strlen($post['mobile'])>=1)){
            return $this->fail('收货人手机号不能为空');
        }
        if(!(isset($post['address']) && strlen($post['address'])>=1)){
            return $this->fail('详细地址不能为空');
        }
        $post['created_at'] = date('Y-m-d H:i:s');
        $post['user_id'] = $this->getMember($request)->getAttribute('id');

        MemberAddres::query()->insert($post);
        return $this->success('创建成功');
    }
    public function save(Request $request): \Illuminate\Http\JsonResponse
    {
        $post = $request->only([
            'name',
            'mobile',
            'city',
            'default',
            'address',
            'sort'
        ]);
        if(isset($post['default']) && $post['default']){
            $post['default'] = 'yes';
        }else{
            $post['default'] = 'no';
        }
        if(isset($post['sort']) && $post['sort']>=1){
            $post['sort'] = intval($post['sort']);
        }else{
            $post['sort'] = 0;
        }
        if(!(isset($post['name']) && strlen($post['name'])>=1)){
            return $this->fail('收货人姓名不能为空');
        }
        if(!(isset($post['mobile']) && strlen($post['mobile'])>=1)){
            return $this->fail('收货人手机号不能为空');
        }
        if(!(isset($post['address']) && strlen($post['address'])>=1)){
            return $this->fail('详细地址不能为空');
        }
        if(!is_string($post['city'])){
            $post['city'] = json_encode($post['city']);
        }
        $post['updated_at'] = date('Y-m-d H:i:s');
        if(!MemberAddres::query()->where(['id'=>$request->input('id'),'user_id'=>$this->getMember($request)->getAttribute('id')])->first()){
            return $this->fail('地址不存在');
        }
        MemberAddres::query()->where(['id'=>$request->input('id')])->update($post);
        return $this->success('修改成功',[]);
    }
    public function delete(Request $request): \Illuminate\Http\JsonResponse
    {
        if(!($address = MemberAddres::query()->where(['id'=>$request->input('id'),'user_id'=>$this->getMember($request)->getAttribute('id')])->first())){
            return $this->fail('地址不存在');
        }
        $address->delete();
        return $this->success('删除成功');
    }
    public function default(Request $request): \Illuminate\Http\JsonResponse
    {
        if(!($address = MemberAddres::query()->where(['id'=>$request->input('id'),'user_id'=>$this->getMember($request)->getAttribute('id')])->first())){
            return $this->fail('地址不存在');
        }
        MemberAddres::query()->where(['user_id'=>$this->getMember($request)->getAttribute('id')])->where('id','<>',$request->input('id'))->update([
            'default'=>'no'
        ]);
        MemberAddres::query()->where(['user_id'=>$this->getMember($request)->getAttribute('id')])->where('id',$request->input('id'))->update([
            'default'=>'yes'
        ]);
        return $this->success('设置成功');
    }
    public function detail(Request $request): \Illuminate\Http\JsonResponse
    {
        if(!($address = MemberAddres::query()->where(['id'=>$request->input('id'),'user_id'=>$this->getMember($request)->getAttribute('id')])->first())){
            return $this->fail('地址不存在');
        }
        return $this->success('获取成功',[
            'info'=>$address
        ]);
    }

}
