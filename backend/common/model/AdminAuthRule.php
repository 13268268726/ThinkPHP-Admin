<?php
namespace backend\common\model;

use org\ArrFunc;
use think\Model;

class AdminAuthRule extends Model
{
    protected $resultSetType = 'collection';

    /**
     * 获取头部信息
     * @return array
     */
    public function getTopParent()
    {
        $top = $this
            ->field('name,title')
            ->where(['pid' => 0, 'is_link' => 1, 'status' => 1])
            ->order('o asc')
            ->cache(60)
            ->select();
        return $top->toArray();
    }

    /**
     * 获取全部数据
     *
     * @param array $w 查询条件
     * @param string $type tree获取树形结构 level获取层级结构
     * @return array 结构数据
     *
     * @date:2017-06-28
     * @author:HJM
     */
    public function getTreeData($w, $type = 'tree')
    {
        $name = 'name';
        $child = 'id';
        $parent = 'pid';

        // 获取数据
        $obj = $this
            ->where($w)
            ->order('o asc')
            ->select();
        $data = $obj->toArray();

        // 获取树形或者结构数据
        if ($type == 'tree') {
            $data = ArrFunc::tree($data, $name, $child, $parent);
        } elseif ($type = "level") {
            $data = ArrFunc::channelLevel($data, 0, '&nbsp;', $child);
        }
        return $data;
    }


    /**
     * 获取列表array('模块/控制器/方法'=>ID)
     * @return array
     * @date:2017-06-29
     * @author:HJM
     */
    public function getAllList()
    {
        return $this->cache(60)->column('id', 'name');
    }
}