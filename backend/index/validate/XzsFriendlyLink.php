<?php
namespace backend\index\validate;

use think\Validate;

class XzsFriendlyLink extends Validate
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
        'friend_name' => 'require',
        'friend_url' => 'require',
        'friend_img' => 'require',
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
        'friend_name' => '友情链接名称',
        'friend_url' => '友情链接URL',
        'friend_img' => '友情链接图片',
    ];

}