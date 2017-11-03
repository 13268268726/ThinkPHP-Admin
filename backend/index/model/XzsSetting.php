<?php
namespace backend\index\model;

use think\Model;

class XzsSetting extends Model
{
    protected $resultSetType = 'collection';

    // 开启自动写入创建和更新的时间戳
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    protected $auto = ['admin_id'];
    protected $insert = ['nav_sort' => 0];
    protected $update = [];

    protected function setAdminIdAttr()
    {
        return session('admin.admin_id');
    }
}