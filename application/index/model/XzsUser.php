<?php
namespace app\index\model;

use think\Model;
use think\Request;

class XzsUser extends Model
{
    protected $resultSetType = 'collection';

    // 开启自动写入创建和更新的时间戳
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    protected $auto = ['user_img'];
    protected $insert = ['reg_ip'];
    protected $update = [];

    /**
     * 获取用户注册IP
     * @return mixed
     */
    protected function setRegIpAttr()
    {
        return Request::instance()->ip();
    }

    /**
     * 设置默认头像
     * @param $data
     * @return string
     */
    protected function setUserImgAttr($data)
    {
        return empty($data) ? '' : $data;
    }


    /**
     * 获取一条数据记录
     * @param $w
     * @param bool $flag
     * @return array
     */
    public function getOneData($w, $flag = true)
    {
        // 正常管理员的条件
        if ($flag) {
            $w['user_state'] = 1;
        }

        $obj = $this->get($w);
        if($obj == null){
            return [];
        }
        return $obj->toArray();
    }
}