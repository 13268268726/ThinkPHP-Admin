<?php
namespace app\common\behavior;

use think\Request;
use think\Session;
use think\Url;

//use think\Controller;
//use \traits\controller\Jump;
//load_trait('controller/Jump');  // 引入traits\controller\Jump
class CheckLogin
{
    // 这个报错，使用不了
//    use Jump;
//    use \traits\controller\Jump;

    // 不需要检查是否登录的“模块/控制器/方法”
    private static $notCheckLogin = ['index/User/login', 'index/User/verify','index/User/register'];

    /**
     * 用户访问权限
     * @param mixed $params
     *
     * @date 2017-06-21
     * @author HJM
     */
    public function run(&$params)
    {
        // 当前操作的请求
        $request = Request::instance();
        $mca = $request->module() . '/' . $request->controller() . '/' . $request->action();

        // 检查是否登录
        if (!self::_checkLogin($mca)) {
//            $this->redirect('index/User/login');

            header("location: " . Url::build('index/User/login'));
            exit;
        }
    }

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
