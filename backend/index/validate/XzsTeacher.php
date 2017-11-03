<?php
namespace backend\index\validate;

use think\Validate;

class XzsTeacher extends Validate
{
    /**
     * 验证规则
     * 通过属性的方式直接定义验证规则等信息
     * @var array
     */
    protected $rule = [
        'name' => 'require|length:1,30',
        'job' => 'require|length:1,10',
        'avatar' => 'require',
        'desc' => 'require|length:10,255',
        'is_show' => 'require|in:0,1',
    ];

    /**
     * 设置字段信息
     * 传入field参数批量设置字段的描述信息
     * @var array
     */
    protected $field = [
        'name' => '讲师姓名',
        'desc' => '讲师描述',
        'avatar' => '讲师头像',
        'job' => '讲师职称',
        'is_show' => '讲师是否显示',
    ];
}