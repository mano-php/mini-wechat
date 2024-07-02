<?php

declare(strict_types=1);

namespace ManoCode\MiniWechat\Traits;

use Illuminate\Http\Request;
use ManoCode\Scrm\Models\ScrmUser;
use ManoCode\MiniWechat\Http\Middleware\MemberLoginMiddleware;
use ManoCode\MiniWechat\Library\JWTLibrary;
use ManoCode\MiniWechat\Models\Member;

/**
 * Trait ApiResponseTrait
 * @package App\Traits
 */
trait ApiResponseTrait
{
    protected $member = null;
    /**
     * 获取用户信息
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|object
     * @throws \Exception
     */
    protected function getMember(Request $request)
    {
        if($this->member!==null){
            return $this->member;
        }
        $token = strval($request->header(MemberLoginMiddleware::TOKEN_KEY));
        if(strlen($token)<=0){
            return $this->fail('请先登录',[],900);
        }
        $token_info = collect(JWTLibrary::decode($token));
        if(!($token_info->has('member_id') && intval($token_info->get('member_id'))>=1 && $this->member = ScrmUser::query()->where(['id'=>intval($token_info->get('member_id'))])->first())){
            return $this->fail('请先登录',[],900);
        }
        if($this->member->getAttribute('state') != '0'){
            return $this->fail('您已被禁止登录');
        }
        return $this->member;
    }
    /**
     * 成功
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(string $message = '操作成功', mixed $data = [], int $status = 200): \Illuminate\Http\JsonResponse
    {
        return $this->jsonResponse($status, $message, $data);
    }


    /**
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function fail(string $message = '操作失败', mixed $data = [], int $status = 400): \Illuminate\Http\JsonResponse
    {
        return $this->jsonResponse($status, $message, $data);
    }

    /**
     * @param int $status
     * @param string $message
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(int $status, string $message, $data)
    {
        return response()->json([
            'status' => $status,
            'success'=> $status===200,
            'message' => $message,
            'data' => $data ?? [],
        ])->withHeaders([
            'Access-Control-Allow-Origin'=>\request()->header('Origin')?:'*',
            'Access-Control-Allow-Credentials'=>'true',
            'Access-Control-Allow-Headers'=>'Accept, Content-Type, Authorization, X-Requested-With, '.MemberLoginMiddleware::TOKEN_KEY
        ]);
    }
}
