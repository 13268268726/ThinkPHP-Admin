<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\XzsUser;
use think\captcha\Captcha;
use think\Config;
use think\Loader;
use think\Request;
use think\Session;
use think\Url;

class User extends Base
{
    /**
     * 首页
     *
     * @date 2017-09-05
     * @author HJM
     */
    public function index()
    {

    }

    /**
     * 用户登录
     * @return mixed|\think\response\Json
     *
     * @date 2017-09-05
     * @author HJM
     */
    public function login()
    {
        if (Request::instance()->isPost()) {
            $request = Request::instance()->post();
            $result = $this->validate($request, 'XzsUser.login');
            if (true === $result) {
                $res = Loader::action('Login/run', ['request' => $request], 'event');
            } else {
                // 验证失败
                $res = ['code' => 0, 'message' => $result];
            }
            return json($res);
        } else {
            return $this->fetch();
        }
    }

    /**
     * 用户注册
     * @return mixed|\think\response\Json
     */
    public function register()
    {
        if (Request::instance()->isPost()) {
            $request = Request::instance()->post();
            $validate = $this->validate($request, 'XzsUser.register');
            if (true !== $validate) {
                // 验证失败
                $res = ['code' => 0, 'message' => $validate];
            } else {
                $XzsUser = new XzsUser();
                $result = $XzsUser->allowField(true)->save($request);
                if ($result) {
                    $res = ['data' => ['JumpUrl' => Url::build('login')], 'code' => 1, 'message' => '注册成功'];
                } else {
                    $res = ['code' => 0, 'message' => '注册失败'];
                }
            }
            return json($res);
        } else {
            return $this->fetch();
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
        // 验证码标记前缀，ulg:用户登录;urg:用户注册
        $data = Request::instance()->param();
        $prefix = isset($data['prefix']) ? $data['prefix'] : 'ulg';
        $config = [
            'fontSize' => 11, // 验证码字体大小
            'length' => 4, // 验证码位数
            'useNoise' => false, // 关闭验证码杂点
            'expire' => 300, // 验证码过期时间（s）
            'imageH' => 24, // 验证码图片高度，设置为0为自动计算
            'imageW' => 80, // 验证码图片宽度，设置为0为自动计算
            'fontttf' => mt_rand(6, 9) . '.ttf', // 验证码字体，不设置则随机获取
        ];
        $captcha = new Captcha($config);
        return $captcha->entry($prefix);
    }

    /**
     * 退出登录
     *
     * @date 2017-09-05
     * @author HJM
     */
    public function logout()
    {
        if (self::_checkLogin()) {
            Session::delete('user');
            $this->success('退出成功', 'login');
        } else {
            $this->redirect('login');
        }
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
        return Session::has('user.user_id');
    }

    /**
     * 我的消息
     *
     * @return \think\Response
     */
    public function news()
    {
        $this->assign([
            'left' => self::_left('news'),
        ]);
        return $this->fetch();
    }

    /**
     * 我的学习
     *
     * @return \think\Response
     */
    public function study()
    {
        $this->assign([
            'left' => self::_left('study'),
        ]);
        return $this->fetch();
    }

    /**
     * 我的班级
     *
     * @return \think\Response
     */
    public function grade()
    {
        $this->assign([
            'left' => self::_left('grade'),
        ]);
        return $this->fetch();
    }

    /**
     * 我的关注
     *
     * @return \think\Response
     */
    public function focus()
    {
        $this->assign([
            'left' => self::_left('focus'),
        ]);
        return $this->fetch();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $user_id
     * @return \think\Response
     */
    public function edit($user_id)
    {
        $field = XzsUser::get($user_id);
        $this->assign([
            'field' => $field,
            'left' => self::_left('edit'),
            'userJob' => Config::get('user_job'),
        ]);
        return $this->fetch();
    }

    /**
     * 修改用户信息接口
     * @param Request $request
     * @param int $user_id 用户ID
     * @return array
     */
    public function update(Request $request, $user_id)
    {
        $data = $request->post();

        $XzsUser = new XzsUser();
        $validate = $this->validate($data, 'XzsUser.edit');

        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $result = $XzsUser->allowField(true)->save($data, ['user_id' => $user_id]);
            if ($result) {
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '修改信息成功', 'status' => 1];
            } else {
                $res = ['info' => '修改信息失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 左侧栏数据信息
     * @param string $active
     * @return array
     */
    private function _left($active)
    {
        return [
            'attentionNum' => 10,
            'fans' => 1,
            'active' => $active
        ];
    }
}