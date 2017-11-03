<?php
namespace backend\index\model;

use think\Model;

class XzsFriendlyLink extends Model
{
    // 开启自动写入创建和更新的时间戳
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    protected $auto = ['admin_id'];
    protected $insert = [];
    protected $update = [];

    protected function setAdminIdAttr()
    {
        return session('admin.admin_id');
    }

    /**
     * 批量设置排序
     *
     * @param array $friend_id
     * @param array $friend_sort
     * @return bool|string
     *
     * @author HJM
     * @date 2017-07-05
     */
    public function setSort($friend_id, $friend_sort)
    {
        $data = array();
        foreach ($friend_id as $key => $val) {
            if ($val) {
                $data[] = array(
                    'friend_id' => $val,
                    'friend_sort' => $friend_sort[$key]
                );
            }
        }
        return $this->saveAll($data);
    }


    /**
     * 获取全部友情链接数据
     * @param array $w
     * @param string $order
     * @return array
     *
     * @author HJM
     * @date 2017-07-05
     */
    public function getAllData($w, $order)
    {
        return $this->where($w)->order($order)->select();
    }
}