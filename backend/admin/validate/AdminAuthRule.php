<?php
namespace backend\admin\validate;

use think\Validate;

class AdminAuthRule extends Validate
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
        'title' => 'require',
    ];

    /**
     * 提示信息
     * @var array
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected $message = [
        'verify.require' => '请输入权限名',
    ];

    /**
     * 设置字段信息
     * 传入field参数批量设置字段的描述信息
     * @var array
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected $field = [];
}
