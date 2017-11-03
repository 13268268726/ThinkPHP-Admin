<?php
namespace app\index\model;

use think\Model;

class XzsTeacher extends Model
{
    protected $resultSetType = 'collection';

    /**
     * 获取数据
     * @return array
     */
    public function getLimitData($limit=12)
    {
        $teacher = $this
            ->where(['is_show' => 1])
            ->limit($limit)
            ->order('sort asc')
            ->select();
        return $teacher->toArray();
    }
}