<?php
namespace backend\admin\model;

use org\ArrFunc;
use think\Model;

class AdminAuthRule extends Model
{
    protected $resultSetType = 'collection';

    /**
     * 获取全部数据
     *
     * @param string $type tree获取树形结构 level获取层级结构
     * @return array 结构数据
     *
     * @date:2017-06-28
     * @author:HJM
     */
    public function getTreeData($type = 'tree')
    {
        $name = 'title';
        $child = 'id';
        $parent = 'pid';

        // 获取数据
        $obj = $this
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

    /*public function insertOneData($data)
    {
        $validate = new AdminAuthRule;
        // 调用当前模型对应的AdminAuthRule验证器类进行数据验证
        $result = $validate->validate(true)->save($data);
        if (false === $result) {
            // 验证失败 输出错误信息
            $res = ['message' => $validate->getError(), 'code' => 0];
        } else {
            $res = ['message' => '添加成功', 'code' => 0, 'data' => $validate->id];
        }
        return $res;
    }*/

    /**
     * 删除数据
     * @param int $id 主键ID
     * @return bool 操作是否成功
     */
    public function deleteData($id)
    {
        $count = $this
            ->where(array('pid' => $id))
            ->count();
        if ($count != 0) {
            return false;
        }
        $result = $this->where(['id' => $id])->delete();
        return $result;
    }

    /**
     * 批量设置排序
     *
     * @param array $id
     * @param array $o
     * @return bool|string
     *
     * @author HJM
     * @date 2017-06-26
     */
    public function setSort($id, $o)
    {
        $data = array();
        foreach ($id as $key => $val) {
            if ($val) {
                $data[] = array(
                    'id' => $val,
                    'o' => $o[$key]
                );
            }
        }
        return $this->saveAll($data);
    }

}