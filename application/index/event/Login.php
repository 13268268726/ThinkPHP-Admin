<?php
namespace app\index\event;

use app\index\model\XzsUser;
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
        $AdminMsg = new XzsUser();
        $msg = $AdminMsg->getOneData(['user_name' => $request['user_name']], true);

        if ($msg) {
            if (my_md5($request['user_pwd']) === $msg['user_pwd']) {

                // 保存session
                self::_keepSession($msg);

                // 登录成功，返回用户ID
                $res = [
                    'code' => 1,
                    'message' => '登录成功',
                    'data' => ['user_id' => $msg['user_id'], 'JumpUrl' => url('index/index')],
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
    private static function _keepSession($msg,$logId=0)
    {
        $auth = array(
            'user_id' => $msg['user_id'],
            'user_name' => $msg['user_name'],
            'user_img' => $msg['user_img'],
            'log_id' => $logId
        );
        Session::set('user', $auth);
    }
}