<?php
namespace backend\index\validate;

use think\Validate;

class XzsSetting extends Validate
{
    /**
     * 验证规则
     * 通过属性的方式直接定义验证规则等信息
     * @var array
     *
     * @author HJM
     * @date 2017-06-20
     */
    protected $rule = [
        'title' => 'require',
        'k' => 'require|unique:xzs_setting',
        'field_value' => 'checkFieldType',
        'id' => 'require', // 首页提交更新数据时验证
    ];

    /**
     * 设置字段信息
     * 传入field参数批量设置字段的描述信息
     * @var array
     *
     * @author HJM
     * @date 2017-06-20
     */
    protected $field = [
        'title' => '配置项标题',
        'k' => '配置项名称',
        'field_value' => '类型值',
        'id' => '配置项参数',
    ];

    /**
     * 场景
     * @var array
     *
     * @author HJM
     * @date 2017-06-20
     */
    protected $scene = [
        'columnSet' => ['title', 'k', 'field_value'], // 新增|修改配置项时验证数据
        'valueSet' => ['id'], // 修改配置项信息时验证
    ];

    /**
     * 当选择了“radio”类型时，判断是否输入类型值
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     *
     * @author HJM
     * @date 2017-06-20
     */
    protected function checkFieldType($value, $rule, $data)
    {
        $field_type = $data['field_type'];
        if ($field_type == 'radio' && empty($value)) {
            return false;
        }
        return true;
    }
}
