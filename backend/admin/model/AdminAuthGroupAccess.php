<?php
namespace backend\admin\model;

use think\Model;

class AdminAuthGroupAccess extends Model
{
    /**
     * 删除已经分配的分组
     * @param array $info ['admin_id'=>'管理员ID','group_list'=>['分组ID字符串']]
     * @return bool
     */
    public function insertData($info)
    {
        $w = array('uid' => $info['admin_id']);
        $result1 = $this->where($w)->delete();

        $result2 = true;
        if (isset($info['group_list']) && !empty($info['group_list'])) {
            $data = array();
            foreach ($info['group_list'] as $k => $v) {
                $data[$k] = array(
                    'uid' => $info['admin_id'],
                    'group_id' => $v
                );
            }
            $result2 = $this->saveAll($data);
        }

        return ($result1 || $result2);
    }
}