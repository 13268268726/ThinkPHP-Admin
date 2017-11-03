<?php
namespace backend\index\model;

use think\Model;
use think\Request;

class AdminLoginLog extends Model
{

    protected $pk = 'id';

    // 开启自动写入创建和更新的时间戳
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'into_ts';
    protected $updateTime = false;

    protected $auto = [];
    protected $insert = ['ip', 'into_ts'];
    protected $update = ['quit_ts'];

    /**
     * 获取IP
     * @return string
     *
     * @date 2017-09-07
     * @author HJM
     */
    protected function setIpAttr()
    {
        return Request::instance()->ip(0, true);
    }

    /**
     * 设置退出时间
     * 目的：在新增数据时，quit_ts字段为0；修改数据时，才为当前时间戳
     *
     * @return int
     *
     * @date 2017-09-07
     * @author HJM
     */
    protected function setQuitTsAttr()
    {
        return time();
    }

    /**
     * 添加管理员登录信息登录信息
     *
     * @param int $admin_id 管理员ID
     * @return int
     *
     * @date 2017-09-07
     * @author HJM
     */
    public function createData($admin_id)
    {
        $AdminLoginLog = new AdminLoginLog;

        $AdminLoginLog->data([
            'admin_id' => $admin_id,
        ]);
        $AdminLoginLog->save();
        return $AdminLoginLog->getAttr('id');
    }

    /**
     * 退出登录
     *
     * @param $id
     * @return false|int
     *
     * @date 2017-09-07
     * @author HJM
     */
    public function updateData($id)
    {
        $AdminLoginLog = new AdminLoginLog;
        return $AdminLoginLog->save(['quit_ts' => time()], ['id' => $id]);
    }
}