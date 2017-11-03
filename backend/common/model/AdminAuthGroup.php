<?php
namespace backend\common\model;

use think\Model;

class AdminAuthGroup extends Model
{
    protected $resultSetType = 'collection';

    /**
     * 根据管理员ID获取管理的栏目
     * @param int $admin_id
     * @return array
     * @date:2017-06-29
     * @author:HJM
     */
    public function getAdminGroupInfo($admin_id)
    {
        $obj = $this
            ->alias(['admin_auth_group' => 'g', 'admin_auth_group_access' => 'ug'])
            ->join('admin_auth_group_access', 'g.id=ug.group_id')
            ->where(['ug.uid' => $admin_id])
            ->select();
        return $obj->toArray();
    }




}