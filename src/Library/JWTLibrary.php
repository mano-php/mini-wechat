<?php

namespace Uupt\MiniWechat\Library;


/**
 *
 */
class JWTLibrary
{
    protected static function getMethod(): string
    {
        return 'aes-256-ecb';
    }
    protected static function getIV(): string
    {
        return md5(time().uniqid(),true);
    }
    protected static function getSecret(): string
    {
        if(strlen(env('APP_KEY',''))<=0){
            throw new \Exception('请先配置APP_KEY');
        }
        return strval(env('APP_KEY',''));
    }

    /**
     * @throws \Exception
     */
    public static function encode(mixed $data):string
    {
        return base64_encode(openssl_encrypt(serialize($data),self::getMethod(),self::getSecret(),OPENSSL_RAW_DATA));
    }

    /**
     * @throws \Exception
     */
    public static function decode(string $data):mixed
    {
        return unserialize(openssl_decrypt(base64_decode($data),self::getMethod(),self::getSecret(),OPENSSL_RAW_DATA));
    }
}
