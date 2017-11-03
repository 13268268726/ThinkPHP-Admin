<?php
namespace backend\index\model;

use think\Model;

class AdminMsg extends Model
{
    /**
     * 实现不在数据表的字段也要验证
     * @var bool
     *
     * @date 2017-09-06
     * @author HJM
     */
    protected $field = true;

    /**
     * @param array $w
     * @param bool $flag
     * @return array
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function getOneData($w, $flag = true)
    {
        // 正常管理员的条件
        if ($flag) {
            $w['admin_state'] = 1;
        }

        $AdminMsg = $this->get($w);
        return $AdminMsg->toArray();
    }
}