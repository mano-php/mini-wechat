<?php

namespace ManoCode\MiniWechat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use ManoCode\MiniWechat\Library\JWTLibrary;
use ManoCode\MiniWechat\Models\Member;
use ManoCode\MiniWechat\Traits\ApiResponseTrait;

/**
 *
 */
class MemberLoginMiddleware
{
    use ApiResponseTrait;
    CONST TOKEN_KEY = 'token';

    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(!$request->hasHeader(self::TOKEN_KEY)){
            return $this->fail('请先登录',[],900);
        }
        $token = strval($request->header(self::TOKEN_KEY));
        if(strlen($token)<=0){
            return $this->fail('请先登录',[],900);
        }
        $token_info = collect(JWTLibrary::decode($token));
        if(!($token_info->has('member_id') && intval($token_info->get('member_id'))>=1 && $member_info = Member::query()->where(['id'=>intval($token_info->get('member_id'))])->first())){
            return $this->fail('请先登录',[],900);
        }
        if($member_info->getAttribute('status') !== 'enable'){
            return $this->fail('您已被禁止登录');
        }

        return $next($request);
    }
}
