<?php
namespace app\index\model;

use think\Model;

class XzsBanner extends Model
{
    protected $resultSetType = 'collection';

    /**
     * 获取所有数据
     * @return array
     */
    public function getAllData()
    {
        $banner = $this->where(['banner_is_show' => 1])->select();
        return $banner->toArray();
    }
}