<?php
namespace backend\admin\model;

use think\Db;
use think\Model;
use think\Request;
use think\Config;

class AdminMsg extends Model
{
    protected $resultSetType = 'collection';

    protected $auto = ['admin_ip', 'admin_pwd'];
    protected $insert = ['created_at', 'score' => 0];
    protected $update = ['updated_at'];

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    /**
     * 获取IP地址
     * @return mixed
     */
    protected function setAdminIpAttr()
    {
        return Request::instance()->ip();
    }

    /**
     * 密码加密
     * @param $data
     * @return string
     */
    protected function setAdminPwdAttr($data)
    {
        if (!empty($data)) {
            $password = my_md5($data);
        } else {
            $admin_id = Request::instance()->param('admin_id');
            if ($admin_id) {
                $info = $this->field('admin_pwd')->find($admin_id);
            }

            $password = (isset($info) && !empty($info)) ? $info['admin_pwd'] : '';
        }
        return $password;
    }

    /**
     * 获取分页数据
     * @param $w
     * @return \think\Paginator
     */
    public function getPageData($w)
    {
        $order = get_order('admin_id desc');
        $pageRows = Config::get('paginate.list_rows');
        if ($w) {
            $num = $this->where($w)->count(); // 获取数据总记录
            $obj = $this->where($w)->order($order)->paginate($pageRows, $num);
        } else {
            $num = $this->count();
            $obj = $this->order($order)->paginate($pageRows, $num);
        }
        return $obj;
    }

    /**
     * 获取用户组
     * @param $admin_id
     * @return array
     */
    public function getGroupByID($admin_id)
    {
        $data = Db::name('admin_auth_group_access')
            ->where(['uid'=>$admin_id])
            ->column('group_id');
        return $data;
    }

    /**
     * 获取所有用户（非删除）
     * @return array
     */
    public function getAll()
    {
        $w['admin_state'] = array('neq', 3);
        $obj = $this->field('admin_id,admin_name')
            ->where($w)
            ->select();
        return $obj->toArray();
    }
}
