<?php
namespace backend\admin\model;

use think\Model;
use think\Config;

class AdminAuthGroup extends Model
{
    protected $resultSetType = 'collection';

    protected $auto = [];
    protected $insert = ['created_at', 'status' => 1];
    protected $update = ['updated_at'];

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    /**
     * 获取分页数据
     * @param $w
     * @return \think\Paginator
     */
    public function getPageData($w)
    {
        $order = get_order('id desc');
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
     * 修改数据
     * @param array $map where语句数组形式
     * @param array $data 数据
     * @return bool 操作是否成功
     */
    public function editData($map, $data)
    {
        $result = $this->allowField(true)->save($data, $map);
        return $result;
    }
}
