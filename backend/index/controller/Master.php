<?php
namespace backend\index\controller;

use backend\common\controller\Base;
use backend\index\model\AdminLoginLog;
use think\captcha\Captcha;
use think\Hook;
use think\Loader;
use think\Request;
use think\Session;

class Master extends Base
{
    /**
     * 重定向
     *
     * @date 2017-09-07
     * @author HJM
     */
    public function index()
    {
        $path = self::_checkLogin() ? 'index/index/index' : 'index/master/login';
        $this->redirect($path);
    }

    /**
     * 登录页面及登录表单处理
     * @return array|string
     *
     * @date 2017-09-05
     * @author HJM
     */
    public function login()
    {
        if (Request::instance()->isAjax()) {
            $request = Request::instance()->post();
            $result = $this->validate($request, 'AdminMsg.login');
            if (true === $result) {
                $res = Loader::action('Login/run', ['request' => $request], 'event');
            } else {
                // 验证失败
                $res = ['code' => 0, 'message' => $result];
            }
            return json($res);
        } else {
            $cookie = Loader::action('Login/getCookie', '', 'event');
            $this->assign('cookie', $cookie);
            return $this->fetch();
        }
    }

    /**
     * 退出登录
     *
     * @date 2017-06-16
     * @author HJM
     */
    public function logout()
    {
        if (self::_checkLogin()) {
            $logId = Session::get('admin.log_id');
            $AdminLoginLog = new AdminLoginLog();
            $AdminLoginLog->updateData($logId);

            Session::delete('admin');
            $this->success('退出成功', 'login');
        } else {
            $this->redirect('login');
        }
    }

    /**
     * 验证码
     * @return \think\Response
     *
     * @date 2017-09-05
     * @author HJM
     */
    public function verify()
    {
        $config = [
            'fontSize' => 14, // 验证码字体大小
            'length' => 4, // 验证码位数
            'useNoise' => false, // 关闭验证码杂点
            'expire' => 300, // 验证码过期时间（s）
            'imageH' => 30, // 验证码图片高度，设置为0为自动计算
            'imageW' => 100, // 验证码图片宽度，设置为0为自动计算
            'fontttf' => mt_rand(6, 9) . '.ttf', // 验证码字体，不设置则随机获取
        ];
        $captcha = new Captcha($config);
        return $captcha->entry('lg');
    }

    /**
     * 检查是否登录
     *
     * @return bool
     *
     * @date 2017-09-07
     * @author HJM
     */
    private static function _checkLogin()
    {
        // 执行 app\common\behavior\DetectingRights行为类的 isSignedIn方法
        return Hook::exec('app\\common\\behavior\\DetectingRights', 'isSignedIn');
    }

    /**
     * 空操作
     * 系统在找不到指定的操作方法的时候，会定位到空操作（_empty）方法来执行
     * 利用这个机制，我们可以实现错误页面和一些URL的优化
     *
     * @return string
     *
     * @date 2017-09-05
     * @author HJM
     */
    public function _empty()
    {
        return $this->fetch('master/404');
    }
}
