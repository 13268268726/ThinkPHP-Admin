<?php
namespace backend\admin\validate;

use think\Validate;

class AdminAuthGroup extends Validate
{
    /**
     * 验证规则
     * 通过属性的方式直接定义验证规则等信息
     * @var array
     *
     * @date 2017-09-08
     * @author HJM
     */
    protected $rule = [
        'title' => 'require|max:25|unique:admin_auth_group',
    ];

    /**
     * 设置字段信息
     * 传入field参数批量设置字段的描述信息
     * @var array
     *
     * @date 2017-09-08
     * @author HJM
     */
    protected $field = [
        'title' => '分组名称',
    ];

    /**
     * 提示信息
     * @var array
     *
     * @date 2017-09-08
     * @author HJM
     */
    /*protected $message = [
        'title.require' => '请输入分组名称',
        'title.max' => '分组名称最多只能输入25个字符',
        'title.unique' => '分组名称已经存在，请重新输入',
    ];*/

}
