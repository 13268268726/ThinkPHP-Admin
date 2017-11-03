<?php
namespace app\common\behavior;

use think\Session;
use think\Controller;

class DetectingRights
{
    // 引入jump类
    use \traits\controller\Jump;

    // 不需要检查是否登录的“模块/控制器/方法”
    private static $notCheckLogin = ['index/user/login', 'index/user/verify'];

    /**
     * 检查是否登录
     * @param $mca
     * @return bool
     *
     * @date 2017-09-07
     * @author HJM
     */
    public function _checkLogin($mca)
    {
        if (in_array($mca, self::$notCheckLogin)) {
            return true;
        }

        if ($this->isSignedIn()) {
            return true;
        }
        return false;
    }

    /**
     * 判断是否有管理员session记录
     * 这里可以添加更多的业务逻辑
     * @return bool
     *
     * @date 2017-09-07
     * @author HJM
     */
    public function isSignedIn()
    {
        return Session::has('user.user_id');
    }
}
