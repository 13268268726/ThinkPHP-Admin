<?php
namespace backend\index\validate;

use think\Validate;

class XzsBanner extends Validate
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
        'banner_name' => 'require',
        'banner_url' => 'require',
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
        'banner_name' => '广告图名称',
        'banner_url' => '广告图URL',
    ];

}