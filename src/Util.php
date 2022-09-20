<?php
/**
 * 工具类
 * @author      church<wolfqong1993@gmail.com>
 * @since        1.0
 */

namespace Church\ApolloClient;

class Util
{
    public static function buildSign($currentTimestamp, $pathWithQuery, $secret)
    {
        $stringToSign = $currentTimestamp . "\n" . $pathWithQuery;
        return base64_encode(hash_hmac('sha1', $stringToSign, $secret, true));
    }

    public static function url2PathWithQuery($url)
    {
        $urlArr = parse_url($url);

        $pathWithQuery = $urlArr['path'];
        if (isset($urlArr['query'])) {
            $pathWithQuery .= '?' . $urlArr['query'];
        }

        return $pathWithQuery;
    }

    public static function currentMills()
    {
        list($usec, $sec) = explode(" ", microtime());
        return round(((float)$usec + (float)$sec) * 1000);
    }
}