<?php
namespace app\index\validate;

use think\captcha\Captcha;
use think\Validate;

class XzsUser extends Validate
{
    /**
     * 验证规则
     * 通过属性的方式直接定义验证规则等信息
     * @var array
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected $rule = [
        'verify' => 'require|checkVerify:ulg',
        'user_name' => 'require|max:25|alphaDash', // alphaDash验证user_name字段的值是否为字母和数字，下划线_及破折号-
        'user_pwd' => 'require|length:6,18|alphaDash',
        're_password' => 'require|length:6,18|confirm:user_pwd',
        'user_email' => 'email',
    ];

    /**
     * 提示信息
     * @var array
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected $message = [
        'verify.checkVerify' => '验证码错误',
        'user_name.require' => '账号必须',
        'user_name.max' => '账号最多不能超过25个字符',
        'user_pwd.require' => '密码只能在6~18位之间',
        'user_email' => '邮箱格式错误',
    ];

    /**
     * 设置字段信息
     * 传入field参数批量设置字段的描述信息
     * @var array
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected $field = [
        'user_name' => '管理员账号',
        'user_pwd' => '管理员密码',
        're_password' => '重复密码',
        'user_email' => '邮箱',
        'verify' => '验证码',
    ];

    /**
     * 场景
     * @var array
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected $scene = [
        'login' => ['verify', 'user_name', 'user_pwd'], // 登录时要验证的字段
        'register' => ['verify' => 'require|checkVerify:urg', 'user_name', 'user_pwd'], // 注册时要验证的字段
        'edit' => ['user_name'], // 修改时要验证的字段
    ];

    /**
     * 检查验证码是否正确
     * @param $value
     * @param $rule
     * @return bool
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected function checkVerify($value, $rule)
    {
        $captcha = new Captcha();
        $result = $captcha->check($value, $rule);
        return $result;
    }
}
