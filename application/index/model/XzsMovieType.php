<?php
namespace app\index\model;

use org\ArrFunc;
use think\Model;

class XzsMovieType extends Model
{
    protected $resultSetType = 'collection';

    /**
     * 获取全部数据
     *
     * @param string $type tree获取树形结构 level获取层级结构
     * @param string $order 排序方式
     * @return array 结构数据
     *
     *
     * @author HJM
     * @date 2017-10-19
     */
    public function getTreeData($type = 'tree', $order = '')
    {
        $name = 'mv_type_name';
        $child = 'mv_type_id';
        $parent = 'mv_type_pid';

        $w = ['is_show' => 1];
        // 判断是否需要排序
        if (empty($order)) {
            $data = $this->where($w)->select();
        } else {
            $data = $this->where($w)->order($order)->select();
        }

        if ($data == null) {
            return [];
        }

        $data = $data->toArray();

        // 获取树形或者结构数据
        if ($type == 'tree') {
            $data = ArrFunc::tree($data, $name, $child, $parent);
        } elseif ($type = "level") {
            $data = ArrFunc::channelLevel($data, 0, '&nbsp;', $child, $parent);
        }
        return $data;
    }


    public function getTypeData($type_pid)
    {
        return $this->where(['xzs_type_pid' => $type_pid])->select();
    }


}