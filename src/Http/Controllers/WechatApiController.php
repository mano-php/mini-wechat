<?php

namespace ManoCode\MiniWechat\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Mano\Crm\Models\CrmUser;
use ManoCode\MiniWechat\Library\EasyWechatLibrary;
use ManoCode\MiniWechat\Library\JWTLibrary;
use ManoCode\MiniWechat\Models\WechatBind;
use ManoCode\MiniWechat\Traits\ApiResponseTrait;

/**
 * 微信接口控制器
 */
class WechatApiController
{
    use ApiResponseTrait;

    /**
     * 微信code 登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function codeLogin(Request $request): \Illuminate\Http\JsonResponse
    {
        $code = strval($request->input('code'));
        if (strlen($code) <= 0) {
            return $this->fail('code不能为空');
        }
        try {
            $response = collect(EasyWechatLibrary::getMiniAppApplication()->getUtils()->codeToSession($code));
        } catch (\Throwable $throwable) {
            return $this->fail("登录失败：code无效");
//            return $this->fail("code无效：{$throwable->getMessage()}");
        }
        $openid = $response->has('openid') ? strval($response->get('openid')) : '';
        $unionid = $response->has('unionid') ? strval($response->get('unionid')) : '';
        /**
         * 判断用户是否注册
         */
        if ($wechatOpenIdBind = WechatBind::query()->where(['openid' => $openid])->first()) {
            return $this->loginDone(intval($wechatOpenIdBind->getAttribute('user_id')));
        }
        /**
         * 如果其他平台绑定过 则自动创建关联关系
         */
        if (strlen($unionid) >= 1 && ($wechatUnionIdBind = WechatBind::query()->where(['unionid' => $openid])->first())) {
            // 通过其他平台用户信息、创建小程序绑定
            $miniAppWechatBind = new WechatBind();
            $miniAppWechatBind->setAttribute('user_id', $wechatUnionIdBind->getAttribute('user_id'));
            $miniAppWechatBind->setAttribute('openid', $openid);
            $miniAppWechatBind->setAttribute('unionid', $unionid);
            $miniAppWechatBind->setAttribute('platform', 'mini-wechat');
            $miniAppWechatBind->setAttribute('state', 'enable');
            $miniAppWechatBind->setAttribute('created_at', date('Y-m-d H:i:s'));
            $miniAppWechatBind->save();
            return $this->loginDone(intval($wechatUnionIdBind->getAttribute('user_id')));
        } else {
            /**
             * 判断是否需要手机号绑定
             */
            if (strval(EasyWechatLibrary::getConfig('other', 'force_bind_mobile')) === 'yes') {
                // 强制绑定手机号 返回绑定key
                return $this->success('需要绑定手机号', [
                    'bind_key' => JWTLibrary::encode([
                        'openid' => $openid,
                        'unionid' => $unionid
                    ])
                ], 600);
            } else {
                /**
                 * 创建用户
                 */
                $member = new CrmUser();
                $member->setAttribute('created_at', date('Y-m-d H:i:s'));
                $member->save();
                /**
                 * 创建微信小程序绑定
                 */
                $miniAppWechatBind = new WechatBind();
                $miniAppWechatBind->setAttribute('user_id', $member->getAttribute('id'));
                $miniAppWechatBind->setAttribute('openid', $openid);
                if (strlen($unionid) >= 1) {
                    $miniAppWechatBind->setAttribute('unionid', $unionid);
                }
                $miniAppWechatBind->setAttribute('platform', 'mini-wechat');
                $miniAppWechatBind->setAttribute('state', 'enable');
                $miniAppWechatBind->setAttribute('created_at', date('Y-m-d H:i:s'));
                $miniAppWechatBind->save();
                return $this->loginDone(intval($member->getAttribute('id')));
            }
        }
    }

    /**
     * @param int $member_id
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    protected function loginDone(int $member_id, string $msg = '登录成功'): \Illuminate\Http\JsonResponse
    {
        if (!($member = CrmUser::query()->where(['id' => $member_id])->first())) {
            return $this->fail("用户不存在");
        }
        // 查询微信信息
        if (($wechat = WechatBind::query()->where(['user_id' => $member_id, 'platform' => 'mini-wechat'])->first()) && strlen($member->getAttribute('mobile')) <= 0) {
            if (strval(EasyWechatLibrary::getConfig('other', 'force_bind_mobile')) === 'yes') {
                // 强制绑定手机号 返回绑定key
                return $this->success('需要绑定手机号', [
                    'bind_key' => JWTLibrary::encode([
                        'openid' => $wechat->getAttribute('openid'),
                        'unionid' => $wechat->getAttribute('unionid')
                    ])
                ], 600);
            }
        }
        if ($member->getAttribute('state') != 0) {
            return $this->fail("您已被禁止登录");
        }
        // 直接登录
        $token = $this->createToken($member_id);
        return $this->success('登录成功', [
            'token' => $token
        ]);
    }

    /**
     * 保存资料
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function saveInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        $info = collect($request->only([
            'nickname',
            'avatar',
            'sex',
            'luck_date',
        ]));
        $updateData = [];
        if ($info->has('nickname') && strlen($info->get('nickname')) >= 1) {
            $updateData['nickname'] = strval($info->get('nickname'));
        }
        if ($info->has('avatar') && strlen($info->get('avatar') >= 1)) {
            $updateData['avatar'] = strval($info->get('avatar'));
        }
        if ($info->has('sex') && strlen($info->get('sex') >= 1)) {
            $updateData['sex'] = strval($info->get('sex'));
        }
        if ($info->has('luck_date') && strlen($info->get('luck_date') >= 1)) {
            $updateData['luck_date'] = strval($info->get('luck_date'));
        }
        if (count($updateData) === 0) {
            return $this->fail('请传入要修改的值');
        }
        CrmUser::query()->where(['id' => intval($this->getMember($request)->getAttribute('id'))])->update($updateData);
        return $this->success('修改成功');
    }

    /**
     * 获取用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->success('修改成功', [
            'id' => $this->getMember($request)->getAttribute('id'),
            'nickname' => $this->getMember($request)->getAttribute('nickname'),
            'mobile' => $this->getMember($request)->getAttribute('mobile'),
            'avatar' => $this->getMember($request)->getAttribute('avatar'),
            'sex' => $this->getMember($request)->getAttribute('sex'),
            'luck_date' => $this->getMember($request)->getAttribute('luck_date')
        ]);
    }

    /**
     * 上传文件
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        // 验证请求中是否包含上传的文件
        if ($request->hasFile('image')) {
            // 获取上传的文件
            $image = $request->file('image');

            // 生成一个随机文件名
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
            if(!is_dir(base_path('public/uploads'))){
                mkdir(base_path('public/uploads'));
            }
            file_put_contents(base_path('public/uploads').DIRECTORY_SEPARATOR.$fileName,$image->getContent());
            // 保存文件到指定目录
//            $image->storeAs(base_path('public/uploads1'), $fileName);

            // 获取请求的协议和域名
            $protocol = $request->getScheme();
            $domain = $request->getHttpHost();

            // 构建完整的文件路径
            $filePath = $protocol . '://' . $domain . '/uploads/' . $fileName;
            return $this->success('上传成功',[
                'path'=>$filePath
            ]);
        } else {
            return $this->fail('未上传文件');
        }
    }

    /**
     * 绑定手机
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function bindMobile(Request $request): \Illuminate\Http\JsonResponse
    {
        $bind_key = strval($request->post('bind_key', ''));
//        $mobile = strval($request->post('mobile',''));
        // 微信小程序获取手机号的 code
        $code = strval($request->post('code', ''));
        if (strlen($bind_key) <= 0) {
            return $this->fail('参数错误');
        }
//        if(strlen($mobile)<=0){
//            return $this->fail('手机号错误');
//        }
        try {
            $bind_info = collect(JWTLibrary::decode($bind_key));
        } catch (\Throwable $throwable) {
            return $this->fail('绑定失败');
        }
        if (!($bind_info->get('openid') && strlen(strval($bind_info->get('openid'))) >= 1)) {
            return $this->fail('绑定信息失效');
        }

        // 获取手机号
        $mobileResponse = collect(json_decode(EasyWechatLibrary::getMiniAppApplication()->getClient()->request('POST', 'wxa/business/getuserphonenumber', [
            'json' => [
                'code' => $code
            ]
        ])->getContent(), true));
        if (!($mobileResponse->get('errcode') === 0 && $mobileResponse->has('phone_info'))) {
            return $this->fail('手机号验证失败', $mobileResponse);
        }
        $mobile = Arr::get($mobileResponse->get('phone_info'), 'purePhoneNumber');
        if (strlen($mobile) <= 0) {
            return $this->fail('手机号不能为空');
        }

        DB::beginTransaction();

        try {
            // 用户创建检测
            if (!($member = CrmUser::query()->where(['mobile' => $mobile])->first())) {
                $member = new CrmUser();
                $member->setAttribute('nickname', '');
                $member->setAttribute('avatar', '');
                $member->setAttribute('mobile', $mobile);
                $member->setAttribute('state', 0);
                $member->setAttribute('created_at', date('Y-m-d H:i:s'));
                $member->save();
            }
            $isUpdate = false;
            // 没有绑定手机号、更新手机号
            if (strlen(strval($member->getAttribute('mobile'))) <= 0) {
                $isUpdate = true;
                $member->setAttribute('mobile', $mobile);
                $member->save();
            }
            if (!($bindModel = WechatBind::query()->where(['openid' => $bind_info->get('openid')])->first())) {
                $bindModel = new WechatBind();
                $bindModel->setAttribute('openid', $bind_info->get('openid'));
                if ($bind_info->has('unionid') && strlen(strval($bind_info->get('unionid'))) >= 1) {
                    $bindModel->setAttribute('unionid', $bind_info->get('unionid'));
                }
                $bindModel->setAttribute('platform', 'mini-wechat');
                $bindModel->setAttribute('user_id', $member->getAttribute('id'));
                $bindModel->setAttribute('state', 'enable');
                $bindModel->setAttribute('created_at', date('Y-m-d H:i:s'));
                $bindModel->save();
            } else {
                if ((!$isUpdate) && intval($bindModel->getAttribute('user_id')) >= 1) {
                    throw new \Exception('此微信已被其他账号绑定');
                }
                if ($bindModel->getAttribute('user_id') != $member->getAttribute('id')) {
                    $bindModel->setAttribute('user_id', $member->getAttribute('id'));
                    $bindModel->save();
                }
            }
            DB::commit();
            return $this->loginDone($member->getAttribute('id'), '绑定成功');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return $this->fail("绑定失败:{$throwable->getMessage()}");
        }
    }

    /**
     * @param int $member_id
     * @return string
     * @throws \Exception
     */
    protected function createToken(int $member_id): string
    {
        return JWTLibrary::encode([
            'member_id' => $member_id,
            'login_time' => date('Y-m-d H:i:s')
        ]);
    }
}
