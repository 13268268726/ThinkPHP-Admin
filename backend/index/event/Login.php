<?php
namespace backend\index\event;

use backend\index\model\AdminLoginLog;
use backend\index\model\AdminMsg;
use think\Config;
use think\Cookie;
use think\Session;

class Login
{
    /**
     * 登录审核方法
     * @param array $request
     * @return array
     *
     * @date 2017-09-07
     * @author HJM
     */
    public function run($request)
    {
        $AdminMsg = new AdminMsg();
        $msg = $AdminMsg->getOneData(['admin_name' => $request['admin_name']], true);

        if ($msg) {
            if (my_md5($request['admin_pwd']) === $msg['admin_pwd']) {

                // 记录日志
                $AdminLoginLog = new AdminLoginLog();
                $logId = $AdminLoginLog->createData($msg['admin_id']);

                // 保存session
                self::_keepSession($msg, $logId);

                // 保存cookie
                self::_keepCookie($request);

                // 登录成功，返回用户ID
                $res = [
                    'code' => 1,
                    'message' => '登录成功',
                    'data' => ['admin_id' => $msg['admin_id'], 'JumpUrl' => url('index/index')],
                ];
            } else {
                $res = ['code' => 0, 'message' => '密码错误'];
            }
        } else {
            $res = ['code' => 0, 'message' => '用户不存在或被禁用'];
        }
        return $res;
    }

    /**
     * 记录session
     * @param array $msg
     * @param int $logId
     *
     * @date 2017-09-07
     * @author HJM
     */
    private static function _keepSession($msg, $logId)
    {
        $auth = array(
            'admin_id' => $msg['admin_id'],
            'admin_name' => $msg['admin_name'],
            'admin_img' => $msg['admin_img'],
            'log_id' => $logId
        );
        Session::set('admin', $auth);
    }

    /**
     *
     * 记录 加密cookie
     * @param $request
     *
     * @date 2017-09-07
     * @author HJM
     */
    private static function _keepCookie($request)
    {
        // 记住账号密码
        if (isset($request['remember']) && $request['remember'] == 1) {
            $str = json_encode(array(
                'admin_name' => $request['admin_name'],
                'admin_pwd' => $request['admin_pwd'],
                'remember' => 1
            ));
            $cookieStr = cookie_code($str, 'ENCODE', Config::get('admin_cookie_key'), 0);
            Cookie::set('AdminInfo', $cookieStr);
        } else {
            // 删除cookie
            Cookie::delete('AdminInfo');
        }
    }

    /**
     * 获取 解密cookie
     * @return array
     *
     * @date 2017-09-07
     * @author HJM
     */
    public function getCookie()
    {
        if (Cookie::has('AdminInfo')) {
            $getCookie = Cookie::get('AdminInfo');
            $cookieStr = cookie_code($getCookie, 'DECODE', Config::get('admin_cookie_key'), 0);
            if ($cookieStr) {
                return json_decode($cookieStr, true);
            }
        }
        return [
            'admin_name' => '',
            'admin_pwd' => '',
            'remember' => 0
        ];
    }
}
