<?php
namespace Open\Support\Crypt;

/**
 * PKCS7Encoder class
 *
 * 提供基于PKCS7算法的加解密接口.
 */
class SHA1
{
    public static function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
    {
        //排序
        try {
            $array = [$encrypt_msg, $token, $timestamp, $nonce];
            sort($array, SORT_STRING);
            $str = implode($array);
            return ['errcode' => 0, 'sha1' => sha1($str)];
        } catch (Exception $e) {
            return ['errcode' => -1, 'errmsg' => $e->getMessage()];
        }
    }
}
