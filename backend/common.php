<?php
// 应用公共文件

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param int $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param bool $suffix 截断显示字符
 * @return string
 */
function str_cut($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
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

/**
 * 加密解密
 * @param string $string
 * @param string $operation
 * @param string $key
 * @param int $expiry
 * @return string
 */
function cookie_code($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;
    // 密匙
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);
    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
        substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
    // 解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
        sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // 验证数据有效性，请看未加密明文的格式
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() == 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)
        ) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 判断是否ajax提交表单
 * @return bool
 */
function is_ajax()
{
    if (\think\Request::instance()->isAjax()) {
        \think\Config::set('token.token_on', false);
        return true;
    }
    header('Content-Type:text/html;charset=utf-8');
    exit('页面不存在！');
}

/**
 * 判断是否post提交表单
 * @return bool
 */
function is_post()
{
    if (\think\Request::instance()->isPost()) {
        return true;
    }
    header('Content-Type:text/html;charset=utf-8');
    exit('页面不存在！');
}

/**
 * 获取排序的信息
 * @param string $default 默认排序，例：ID desc
 * @param int $type 返回类型，0:字符串，1数组
 * @return string 例："ID desc"
 */
function get_order($default = '', $type = 0)
{
    $interval = '__'; // 排序分割符
    $order = \think\Request::instance()->param('order'); // 排序信息 排序的字段和排序规则，UserID__desc

    if (empty($order)) {
        $sortName = '';
        $sort = '';
    } else {
        list($sortName, $sort) = explode($interval, $order);
    }
    $sort = strtolower($sort);

    if ($type) {
        return array($sortName, $sort);
    } else {
        if (empty($order)) {
            return $default;
        }
        return sprintf('%s %s', $sortName, $sort);
    }
}


/**
 * 设置排序
 * @param string $column 需要排序的字段
 * @param string $ColumnZh 需要排序的字段的名字
 * @return string a标签
 */
function set_order($column, $ColumnZh)
{
    $interval = '__'; // 排序分割符
    list($sortName, $sort) = get_order('', 1);

    if ($sortName == $column) {
        if ($sort == 'desc') {
            $NextSort = 'asc'; // 下一次排序方式
            $NowTip = '&uarr;'; // 当前排序图标
        } else {
            $NextSort = 'desc';
            $NowTip = '&darr;';
        }
    } else {
        $NextSort = 'desc'; // 默认下一排序从大到小
        $NowTip = ''; // 默认当前排序图标为空
    }
    $parameter = $_GET; // 获取参数
    $parameter['order'] = '[ORDER]';
    $url = url(\think\Request::instance()->action(), $parameter);
    $ColumnSort = sprintf('%s%s%s', $column, $interval, $NextSort);
    $href = str_replace(urlencode('[ORDER]'), $ColumnSort, $url);
    return '<a href="' . $href . '">' . $ColumnZh . $NowTip . '</a>';
}

/**
 * 获取当前页码
 * @param $param
 * @return array|int
 */
function get_current($param)
{
    $pageParam = \think\Config::get('paginate.var_page');
    $request = \think\Request::instance();
    if ($request->has($pageParam)) {
        $curPage = $request->get($pageParam);
    }
    $curPage = (isset($curPage) && $curPage) ? $curPage : 1;
    if (is_array($param)) {
        $param[$pageParam] = $curPage;
        return $param;
    }
    return $curPage;
}

/**
 * 获取配置项的html
 * 页面 Setting/index
 * @param array $val
 * @return string
 */
function get_setting_html($val)
{
    $html = '';
    switch ($val['field_type']) {
        case 'input':
            $html = '<input type="text" class="form-control" name="v" value="' . $val['v'] . '">';
            break;
        case 'textarea':
            $html = '<textarea type="text" class="autosize-transition form-control" name="v">' . $val['v'] . '</textarea>';
            break;
        case 'radio':
            $str = '';
            $arr = explode(',', $val['field_value']);
            foreach ($arr as $m => $n) {
                $r = explode('|', $n);
                $c = $val['v'] == $r[0] ? 'checked' : '';
                $str .= '<label class="checkbox-inline">
                                <input type="radio" class="ace" name="v" value="' . $r[0] . '" ' . $c . ' />
                                <span class="lbl"> ' . $r[1] . ' </span>
                            </label>';
            }
            $html = $str;
            break;
    }
    return $html;
}
