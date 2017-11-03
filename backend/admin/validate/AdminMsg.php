<?php
namespace backend\admin\validate;

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
        'admin_name' => 'require|max:25|unique:admin_msg',
        'admin_email' => 'require|email',
        'admin_sex' => 'require|number|in:0,1',
        'admin_state' => 'require|number|in:1,2',
        'admin_pwd' => 'require|alphaDash|length:6,12',
        'repassword' => 'require|alphaDash|length:6,12|confirm:admin_pwd',
        'admin_img' => 'require',
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
        'admin_name' => '账号',
        'admin_email' => '邮箱',
        'admin_sex' => '性别',
        'admin_state' => '状态',
        'admin_pwd' => '密码',
        'repassword' => '确认密码',
        'admin_img' => '头像',
    ];

    /**
     * 提示信息
     * @var array
     *
     * @date 2017-09-06
     * @author HJM
     */
    /*protected $message = [
        'verify.require' => '请输入权限名',
    ];*/

    /**
     * 定义验证场景
     * @var array
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected $scene = [
        'step1'  =>  ['admin_name','admin_email','admin_sex','admin_state'],
        'step2'  =>  ['admin_pwd','repassword'],
        'step3'  =>  ['admin_img'],
    ];

}
