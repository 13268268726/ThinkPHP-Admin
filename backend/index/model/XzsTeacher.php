<?php
namespace backend\index\model;

use think\Config;
use think\Model;

class XzsTeacher extends Model
{
    // 开启自动写入创建和更新的时间戳
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    protected $auto = ['admin_id'];
    protected $insert = [];
    protected $update = [];

    protected function setAdminIdAttr()
    {
        return session('admin.admin_id');
    }

    /**
     * 批量设置排序
     *
     * @param array $teacher_id
     * @param array $sort
     * @return bool|string
     */
    public function setSort($teacher_id, $sort)
    {
        $data = array();
        foreach ($teacher_id as $key => $val) {
            if ($val) {
                $data[] = array(
                    'teacher_id' => $val,
                    'sort' => $sort[$key]
                );
            }
        }
        return $this->saveAll($data);
    }


    /**
     * 获取分页数据
     * @param $w
     * @return \think\Paginator
     */
    public function getPageData($w)
    {
        $order = get_order('sort asc');
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
}