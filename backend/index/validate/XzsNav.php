<?php
namespace backend\index\validate;

use think\Validate;

class XzsNav extends Validate
{
    /**
     * 验证规则
     * 通过属性的方式直接定义验证规则等信息
     * @var array
     *
     * @author HJM
     * @date 2017-07-05
     */
    protected $rule = [
        'nav_name' => 'require|max:10',
        'nav_url' => 'require',
    ];

    /**
     * 设置字段信息
     * 传入field参数批量设置字段的描述信息
     * @var array
     *
     * @author HJM
     * @date 2017-07-05
     */
    protected $field = [
        'nav_name' => '导航栏名称',
        'nav_url' => '导航栏URL',
    ];
}
