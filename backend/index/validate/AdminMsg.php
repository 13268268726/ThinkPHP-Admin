<?php
namespace backend\index\validate;

use think\captcha\Captcha;
use think\Validate;

class AdminMsg extends Validate
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
        'verify' => 'require|checkVerify',
        'admin_name' => 'require|max:20|alphaDash', // alphaDash验证admin_name字段的值是否为字母和数字，下划线_及破折号-
        'admin_pwd' => 'require|length:6,18|alphaDash',
        'age' => 'number|between:1,120',
        'email' => 'email',
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
        'admin_name.require' => '账号必须',
        'admin_name.max' => '账号最多不能超过25个字符',
        'admin_pwd.require' => '密码只能在6~18位之间',
        'age.number' => '年龄必须是数字',
        'age.between' => '年龄只能在1-120之间',
        'email' => '邮箱格式错误',
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
        'admin_name' => '管理员账号',
        'admin_pwd' => '管理员密码',
        'age' => '年龄',
        'email' => '邮箱',
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
        'login' => ['verify', 'admin_name', 'admin_pwd'], // 登录时要验证的字段
    ];

    /**
     * 检查验证码是否正确
     * @param $value
     * @return bool
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected function checkVerify($value)
    {
        $captcha = new Captcha();
        $result = $captcha->check($value, 'lg');
        return $result;
    }
}
