<?php
namespace backend\common\behavior;

use think\Request;
use think\Session;
use think\Controller;

class DetectingRights
{
    // 引入jump类
    use \traits\controller\Jump;

    // 不需要检查是否登录的“模块/控制器/方法”
    private static $notCheckLogin = ['index/Master/login', 'index/Master/verify'];

    // 不用检查权限就能访问的（模块/控制器/方法）
    private static $notCheckPower = [
        'index/Index/index',
        'index/Master/index',
        'index/Master/login',
        'index/Master/verify',
        'index/Master/_empty',
        'index/Error/index',
        'index/Error/_empty',
    ];

    /**
     * 用户访问权限
     * @param mixed $params
     *
     * @date 2017-06-21
     * @author HJMc
     */
    public function run(&$params)
    {
        // 当前操作的请求
        $request = Request::instance();
        $mca = $request->module() . '/' . $request->controller() . '/' . $request->action();

        // 检查是否登录
        if (!self::_checkLogin($mca)) {
            $this->redirect('index/Master/login');
        }

        // 检查是否有权限
        $admin_id = Session::get('admin.admin_id');
        if (!self::_checkPower($mca, $admin_id)) {
            if ($request->isAjax() || $request->isPost()) {
                echo json_encode([
                    'info' => '你没有权限访问',
                    'status' => 0
                ]);
            } else {
                $this->error('你没有权限访问，页面跳转中...', 'index/Index/index');
            }
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
     * 检查是否有权限
     * @param $mca
     * @param $admin_id
     * @return bool
     *
     * @date 2017-09-07
     * @author HJM
     */
    private static function _checkPower($mca, $admin_id)
    {
        if (in_array($mca, self::$notCheckPower) || $admin_id == 1) return true;

        // 判断权限
        $auth = new Auth();
        $result = $auth->check($mca, $admin_id);

        if ($result) return true;
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
        return Session::has('admin.admin_id');
    }
}
