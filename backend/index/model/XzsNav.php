<?php
namespace backend\index\model;

use org\ArrFunc;
use think\Model;

class XzsNav extends Model
{
    protected $resultSetType = 'collection';

    // 开启自动写入创建和更新的时间戳
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    protected $auto = ['admin_id'];
    protected $insert = ['nav_sort'=>0];
    protected $update = [];

    protected function setAdminIdAttr()
    {
        return session('admin.admin_id');
    }

    /**
     * 批量设置排序
     *
     * @param array $nav_id
     * @param array $nav_sort
     * @return bool|string
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function setSort($nav_id, $nav_sort)
    {
        $data = array();
        foreach ($nav_id as $key => $val) {
            if ($val) {
                $data[] = array(
                    'nav_id' => $val,
                    'nav_sort' => $nav_sort[$key]
                );
            }
        }
        return $this->saveAll($data);
    }


    /**
     * 获取全部数据
     *
     * @param string $type tree获取树形结构 level获取层级结构
     * @param string $order 排序方式
     * @return array 结构数据
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function getTreeData($type = 'tree', $order = '')
    {
        $name = 'nav_name';
        $child = 'nav_id';
        $parent = 'nav_pid';
        // 判断是否需要排序
        if (empty($order)) {
            $data = $this->select();
        } else {
            $data = $this->order($order)->select();
        }

        $data = $data->toArray();

        // 获取树形或者结构数据
        if ($type == 'tree') {
            $data = ArrFunc::tree($data, $name, $child, $parent);
        } elseif ($type = "level") {
            $data = ArrFunc::channelLevel($data, 0, '&nbsp;', $child);
        }
        return $data;
    }


    /**
     * 同步更手册分类的信息，条件：父级ID是3
     *
     * @param array $info array('nav_id'=>'','nav_pid'=>3,'nav_name'=>'');
     * @return bool
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function updateBookType($info)
    {
        $flag = true;
        if ($info['nav_pid'] == 3) {
            $XzsBookType = D('XzsBookType');
            $w = array('type_pid' => 0, 'cate_id' => $info['nav_id']);
            $data = array(
                'type_name' => $info['nav_name'],
                'is_show' => $info['nav_is_show'],
                'updated_at' => NOW_TIME,
                'admin_id' => get_admin_id()
            );

            // 查询是否有记录
            $count = $XzsBookType->where($w)->count();

            if ($count) { // 有记录就修改
                $flag = $XzsBookType->where($w)->setField($data);
            } else { // 没记录就添加
                $data = array_merge($data, $w);
                $flag = $XzsBookType->add($data);
            }
        }
        return $flag;
    }

}