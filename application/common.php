<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 字符截取（对中文、英文都可以进行截取）
 * @param string $string   字符串
 * @param int $start       字符串截取开始位置(下标)
 * @param int $length      字符串截取长度(多少个中文、英文)
 * @param string $charset  字符串编码
 * @param string $dot      截取操作发生时，在被截取字符串最后边增加的字符串
 * @return string
 */
function str_cut(&$string, $start, $length, $charset = "utf-8", $dot = '...')
{
    if (function_exists('mb_substr')) {
        if (mb_strlen($string, $charset) > $length) {//按字符获取长度
            return mb_substr($string, $start, $length, $charset) . $dot;
        }
        return mb_substr($string, $start, $length, $charset);//按字符截取字符串
    } else if (function_exists('iconv_substr')) {
        if (iconv_strlen($string, $charset) > $length) {//
            return iconv_substr($string, $start, $length, $charset) . $dot;
        }
        return iconv_substr($string, $start, $length, $charset);
    }

    $charset = strtolower($charset);
    switch ($charset) {
        case "utf-8" :
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $ar);
            if (func_num_args() >= 3) { //func_num_args()  返回函数的参数个数
                if (count($ar[0]) > $length) {
                    return join("", array_slice($ar[0], $start, $length)) . $dot;
                }
                return join("", array_slice($ar[0], $start, $length));
            } else {
                return join("", array_slice($ar[0], $start));//join()=>implode()
            }
            break;
        default:
            $start = $start * 2;
            $length = $length * 2;
            $strlen = strlen($string);
            $tmpstr = '';
            for ($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $length)) {
                    if (ord(substr($string, $i, 1)) > 129) $tmpstr .= substr($string, $i, 2);
                    else $tmpstr .= substr($string, $i, 1);
                }
                if (ord(substr($string, $i, 1)) > 129) $i++; //返回字符的 ASCII 码值
            }
            if (strlen($tmpstr) < $strlen) $tmpstr .= $dot;
            return $tmpstr;
    }
}

/**
 * 非常规MD5加密方法
 * @param string $str 要加密的字符串
 * @param string $key
 * @return string
 */
function my_md5($str, $key = 'Thinker')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}