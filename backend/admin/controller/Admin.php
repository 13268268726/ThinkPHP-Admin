<?php
namespace backend\admin\controller;

use backend\admin\model\AdminAuthGroupAccess;
use backend\admin\model\AdminMsg;
use backend\common\controller\Base;
use backend\common\model\AdminAuthGroup;
use think\Request;
use think\Url;

class Admin extends Base
{
    private $AdminStateMsg = array(
        0 => '--',
        1 => '正常',
        2 => '锁定',
        3 => '删除',
    );

    static private $table = 'admin_msg';

    /**
     * 显示资源列表
     *
     * @return \think\Response
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function index()
    {
        $admin_name = Request::instance()->get('admin_name');
        $w = ['admin_state' => ['neq', 3]];
        if (!empty($admin_name)) {
            $w['admin_name'] = array('like', "%$admin_name%");
        }

        $AdminMsg = new AdminMsg();
        $list = $AdminMsg->getPageData($w);

        $this->assign([
            'list' => $list,
            'PageInfo' => $list->render(),
            'AdminStateMsg' => $this->AdminStateMsg
        ]);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function create()
    {
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function save(Request $request)
    {
        $data = $request->param();
        $step = (int)$request->post('step');
        $validate = $this->validate($data, "AdminMsg.step$step");
        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            if ($step == 1) { // 步骤1，新增
                $AdminAuthRule = new AdminMsg($data);
                $result = $AdminAuthRule->allowField(true)->save();
                $admin_id = $AdminAuthRule->admin_id;
            } else {
                $AdminMsg = new AdminMsg();
                $admin_id = $data['admin_id'];
                if ($step == 2) { // 步骤2
                    $data = ['admin_pwd' => $data['admin_pwd']];
                } else { // 步骤3
                    $data = ['admin_img' => $data['admin_img']];
                }
                $result = $AdminMsg->update($data, ['admin_id' => $admin_id]);
            }

            if ($result) {
                $res = ['data' => ['admin_id' => $admin_id], 'info' => '设置管理员成功', 'status' => 1];
            } else {
                $res = ['info' => '设置管理员失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        // TODO: this action
        return $this->fetch();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function edit($id)
    {
        // 管理员信息
        $AdminMsg = new AdminMsg();
        $field = $AdminMsg::get($id);

        // 获取已加入用户组
        $fieldGroupData = $AdminMsg->getGroupByID($id);

        // 全部用户组
        $GroupData = AdminAuthGroup::all();

        $this->assign('field', $field);
        $this->assign('fieldGroupData', $fieldGroupData);
        $this->assign('GroupData', $GroupData);

        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $admin_id
     * @return \think\Response
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function update(Request $request, $admin_id)
    {
        is_ajax();
        $data = $request->post();

        $step = (int)$data['SubType'];
        $validate = $this->validate($data, "AdminMsg.step$step");
        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $AdminMsg = new AdminMsg();

            if ($step == 1) { // 基本信息设置
                $column = ['admin_name', 'admin_email', 'admin_sex', 'admin_state'];
            } else if ($step == 2) { // 密码重置
                $column = ['admin_pwd'];
            } else { // 选择头像
                $column = ['admin_img'];
            }

            $result = $AdminMsg->allowField($column)->save($data, ['admin_id' => $admin_id]);
            if ($result) {
                $this->log(self::$table, $admin_id);
                $res = array('data' => array('JumpUrl' => Url::build('index')), 'info' => '修改管理员成功', 'status' => 1);
            } else {
                $res = array('info' => '修改管理员失败', 'status' => 0);
            }
        }
        return json($res);
    }

    /**
     * 改变管理员分组
     *
     * @return \think\response\Json
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function changeGroupApi()
    {
        is_ajax();
        $info = Request::instance()->post();

        // 更新数据
        $AdminAuthGroupAccess = new AdminAuthGroupAccess();
        $result = $AdminAuthGroupAccess->insertData($info);

        if ($result) {
            $res = array('data' => array('JumpUrl' => Url::build('index')), 'info' => '分配管理员分组成功', 'status' => 1);
        } else {
            $res = array('info' => '分配管理员分组失败', 'status' => 0);
        }
        return json($res);
    }

    /**
     * 删除指定资源
     *
     * @param  int $admin_id
     * @return \think\Response
     *
     * @date 2017-09-06
     * @author HJM
     */
    public function delete($admin_id)
    {
        is_ajax();
        $AdminMsg = new AdminMsg();
        $result = $AdminMsg
            ->where('admin_id', $admin_id)
            ->update(['admin_state' => 3]);

        if ($result) {
            $this->log(self::$table, $admin_id);
            $res = array('info' => '删除管理员成功', 'status' => 1);
        } else {
            $res = array('info' => '删除管理员失败', 'status' => 0);
        }
        return json($res);
    }
}
