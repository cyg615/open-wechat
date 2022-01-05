<?php
namespace Open\Support\Crypt;

/**
 * PKCS7Encoder class
 *
 * 提供基于PKCS7算法的加解密接口.
 */
class PrpCrypt
{

    /**
     * 对明文进行加密
     *
     * @param $text
     * @param $appid
     * @param $key
     * @return array
     */
    public static function encrypt($text, $appid, $key)
    {
        try {
            //获得16位随机字符串，填充到明文之前
            $random = self::getRandomStr();
            $text = $random . pack("N", strlen($text)) . $text . $appid;
            //使用自定义的填充方式对明文进行补位填充
            $text = PKCS7Encoder::encode($text);

            $iv = substr($key, 0, 16);
            $encrypted = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
//            $encrypted = substr($encrypted, 0, strlen($encrypted) - 16);


            //print(base64_encode($encrypted));
            //使用BASE64对加密后的字符串进行编码
            return ['errcode' => 0, 'encrypt' => base64_encode($encrypted)];
        } catch (Exception $e) {
            return ErrorCode::$EncryptAESError;
        }
    }

    /**
     * 对密文进行解密
     *
     * @param $encrypted
     * @param $appid
     * @param $key
     * @return array
     */
    public static function decrypt($encrypted, $appid, $key)
    {
        try {
            //使用BASE64对需要解密的字符串进行解码
            $iv = substr($key, 0, 16);
            $decrypted_un64 = base64_decode($encrypted);
            $decrypted = openssl_decrypt($decrypted_un64, 'aes-256-cbc', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
        } catch (Exception $e) {
            return ErrorCode::$DecryptAESError;
        }
        try {
            //去除补位字符
            $result = PKCS7Encoder::decode($decrypted);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) {
                return ErrorCode::$PKCS7EncoderError;
            }
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);

        } catch (Exception $e) {
            return ErrorCode::$IllegalBuffer;
        }
        if ($from_appid != $appid) {
            return array_merge(ErrorCode::$ValidateAppidError,['fromAppId' => $from_appid,'originAppId' => $appid]);
        }
        return ['errcode' => 0, 'decrypt' => $xml_content];

    }


    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    public static function getRandomStr()
    {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

}
