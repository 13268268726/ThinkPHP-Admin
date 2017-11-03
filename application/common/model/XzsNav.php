<?php
namespace app\common\model;

use org\ArrFunc;
use think\Model;

class XzsNav extends Model
{
    protected $resultSetType = 'collection';

    /**
     * 获取头部栏目
     * @param string $type
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getMenu($type = 'level')
    {
        $child = 'nav_id';
        $parent = 'nav_pid';
        $name = 'nav_name';

        // 查询1
//        $nav = XzsNav::all(function($query){
//            $query->where('nav_is_show', 1)->order('nav_sort', 'asc');
//        });

        // 查询2
//        $data = XzsNav::where('nav_is_show',1)->order('nav_sort', 'asc')->select();

        // 查询3
        $nav = $this->where('nav_is_show', 1)->order('nav_sort', 'asc')->select();
        $data = $nav->toArray();

        // 获取树形或者结构数据
        if ($type == 'tree') {
            $data = ArrFunc::tree($data, $name, $child, $parent);
        } else if ($type = "level") {
            $data = ArrFunc::channelLevel($data, 0, '&nbsp;', $child, $parent);
        }

        return $data;
    }
}