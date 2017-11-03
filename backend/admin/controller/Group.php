<?php
namespace backend\admin\controller;

use backend\admin\model\AdminAuthGroup;
use backend\admin\model\AdminAuthGroupAccess;
use backend\admin\model\AdminAuthRule;
use backend\admin\model\AdminMsg;
use backend\common\controller\Base;
use think\Db;
use think\Request;
use think\Url;

class Group extends Base
{
    private static $table = 'admin_auth_group';

    /**
     * 显示资源列表
     *
     * @return \think\Response
     *
     * @author:HJM
     * @date:2017-09-08
     */
    public function index()
    {
        $title = Request::instance()->get('title');
        $w = [];
        if (!empty($admin_name)) {
            $w['$title'] = ['like', "%$title%"];
        }

        $AdminMsg = new AdminAuthGroup();
        $list = $AdminMsg->getPageData($w);

        $this->assign([
            'list' => $list,
            'PageInfo' => $list->render(),
        ]);
        return $this->fetch();
    }

    /**
     * 管理员分组添加 & 修改
     *
     * @param Request $request
     * @return \think\response\Json
     *
     * @author:HJM
     * @date:2017-09-08
     */
    public function formFnApi(Request $request)
    {
        is_ajax();
        $data = $request->param();
        $validate = $this->validate($data, "AdminAuthGroup");
        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            // 分组ID
            $id = Request::instance()->post('id', 0, 'intval');
            if (empty($id)) {
                // 添加
                $AdminAuthRule = new AdminAuthGroup($data);
                $result = $AdminAuthRule->allowField(true)->save();
                $id = $AdminAuthRule->id;
            } else {
                // 修改
                $AdminAuthGroup = new AdminAuthGroup();
                $result = $AdminAuthGroup->allowField(true)->update($data, ['id' => $id]);
            }

            if ($result) {
                $this->log(self::$table, $id);
                $res = ['info' => '提交成功', 'status' => 1];
            } else {
                $res = ['info' => '提交失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 显示编辑资源表单接口
     *
     * @return \think\Response
     *
     * @author:HJM
     * @date:2017-09-08
     */
    public function editApi()
    {
        is_ajax();
        $id = Request::instance()->post('id', 0, 'intval');
        $data = Db::name('AdminAuthGroup')->field('id,title,status')->find($id);

        if ($data) {
            $res = ['data' => $data, 'info' => 'success', 'status' => 1];
        } else {
            $res = ['info' => '该管理员分组不存在或已经被删除了', 'status' => 0];
        }
        return json($res);
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     *
     * @author:HJM
     * @date:2017-09-08
     */
    public function delete($id)
    {
        is_ajax();
        $AdminAuthGroup = new AdminAuthGroup();
        $result = $AdminAuthGroup
            ->where('id', $id)
            ->delete();

        if ($result) {
            $this->log(self::$table, $id);
            $res = ['info' => '删除管理员分组成功', 'status' => 1];
        } else {
            $res = ['info' => '删除管理员分组失败', 'status' => 0];
        }
        return json($res);
    }

    /**
     * 分配权限页面
     * @return mixed
     *
     * @author:HJM
     * @date:2017-09-08
     */
    public function setRule()
    {
        $id = Request::instance()->param('id');
        // 获取用户组数据
        $group_data = Db::name('AdminAuthGroup')->find($id);
        $group_data['rules'] = explode(',', $group_data['rules']);

        // 获取规则数据
        $AdminAuthRule = new AdminAuthRule();
        $rule_data = $AdminAuthRule->getTreeData('level');

        // 获取页面table标签信息
        $table = '';
        if (!empty($rule_data)) {
            $table = \think\Loader::action('RuleTable/init', ['rule_data' => $rule_data, 'group_data' => $group_data], 'event');
        }

        $this->assign([
            'table' => $table,
            'group_data' => $group_data,
        ]);
        return $this->fetch();
    }

    /**
     * 分配权限接口
     *
     * @author:HJM
     * @date:2017-09-08
     */
    public function setRuleApi()
    {
        is_post();
        $data = Request::instance()->post();

        $map = ['id' => $data['id']];
        $rules = implode(',', $data['rule_ids']);
        $AdminAuthGroup = new AdminAuthGroup();
        $result = $AdminAuthGroup->editData($map, ['rules' => $rules]);
        if ($result) {
            $this->success('分配权限成功', Url::build('index'));
        } else {
            $this->error('分配权限失败');
        }
    }

    /**
     * 设置分组管理员页面
     * @return mixed
     *
     * @author:HJM
     * @date:2017-09-08
     */
    public function setAdmin()
    {
        $id = Request::instance()->param('id'); // 分组ID
        $group_data = Db::name('AdminAuthGroup')->find($id);

        // 获取已加入用户组的用户ID
        $fieldUserData = Db::name('AdminAuthGroupAccess')
            ->where(['group_id' => $id])
            ->column('uid');

        // 全部管理员
        $AdminMsg = new AdminMsg();
        $UserData = $AdminMsg->getAll();

        $this->assign([
            'group_data' => $group_data,
            'fieldUserData' => $fieldUserData,
            'UserData' => $UserData
        ]);

        return $this->fetch();
    }

    /**
     * 设置分组管理员接口
     * @return \think\response\Json
     *
     * @author:HJM
     * @date:2017-09-08
     */
    public function setAdminApi()
    {
        is_ajax();
        $info = Request::instance()->post();

        $AdminAuthGroupAccess = new AdminAuthGroupAccess();

        // 删除已经分配的分组
        $w = ['group_id' => $info['group_id']];
        $result1 = $AdminAuthGroupAccess->where($w)->delete();

        $result2 = true;
        if (isset($info['admin_list']) && !empty($info['admin_list'])) {
            $data = [];
            foreach ($info['admin_list'] as $k => $v) {
                $data[$k] = array(
                    'uid' => $v,
                    'group_id' => $info['group_id']
                );
            }
            $result2 = $AdminAuthGroupAccess->saveAll($data);
        }

        if ($result1 || $result2) {
            $this->log('admin_auth_group_access', $info['group_id']);
            $res = ['data' => ['JumpUrl' => Url::build('group/index')], 'info' => '分配管理员分组成功', 'status' => 1];
        } else {
            $res = ['info' => '分配管理员分组失败', 'status' => 0];
        }
        return json($res);
    }
}
