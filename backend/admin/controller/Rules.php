<?php

namespace backend\admin\controller;

use backend\admin\model\AdminAuthRule;
use backend\common\controller\Base;
use think\Request;
use think\Url;

class Rules extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-06-26
     */
    public function index()
    {
        $AdminAuthRule = new AdminAuthRule();
        $data = $AdminAuthRule->getTreeData('tree');

        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 表单接收，分配方法
     *
     * @param Request $request
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-06-26
     */
    public function formFnApi(Request $request)
    {
        is_ajax();
        $json = '';
        switch ($request->param('form_type')) {
            case 1: // 添加
            case 2: // 添加子类
                $json = $this->_save($request);
                break;
            case 3: // 修改
                $json = $this->_update($request, $request->param('id'));
                break;
        }
        return $json;
    }

    /**
     * 添加后台权限
     *
     * @param  \think\Request $request
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-06-26
     */
    private function _save(Request $request)
    {
        $validate = $this->validate($request->param(), 'AdminAuthRule');
        if (true !== $validate) {
            // 验证失败 输出错误信息
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $AdminAuthRule = new AdminAuthRule($request->param());
            // 过滤post数组中的非数据表字段数据
            $result = $AdminAuthRule->allowField(true)->save();
            if ($result) {
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '添加后台权限成功', 'status' => 1];
            } else {
                $res = ['info' => '添加后台权限失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-06-26
     */
    public function edit($id)
    {
        is_ajax();
        $obj = AdminAuthRule::get($id);
        $data = $obj->toArray();
        if (empty($data)) {
            $res = ['info' => '该条记录可能已经被删除', 'status' => 0];
        } else {
            $res = ['data' => $data, 'info' => 'success', 'status' => 1];
        }
        return json($res);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-06-26
     */
    private function _update(Request $request, $id)
    {
        $validate = $this->validate($request->param(), 'AdminAuthRule');
        if (true !== $validate) {
            // 验证失败 输出错误信息
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $AdminAuthRule = new AdminAuthRule();
            // 过滤post数组中的非数据表字段数据
            $result = $AdminAuthRule->allowField(true)->save($request->param(), ['id' => $id]);
            if ($result) {
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '修改后台权限成功', 'status' => 1];
            } else {
                $res = ['info' => '修改后台权限失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-06-26
     */
    public function delete($id)
    {
        $AdminAuthRule = new AdminAuthRule();
        $result = $AdminAuthRule->deleteData($id);
        if ($result) {
            $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '删除成功', 'status' => 1];
        } else {
            $res = ['info' => '请先删除子权限', 'status' => 0];
        }
        return json($res);
    }

    /**
     * 批量设置排序接口
     *
     * @param Request $request
     * @return \think\response\Json
     *
     * @author:HJM
     * @date:2017-06-26
     */
    public function sortApi(Request $request)
    {
        is_ajax();
        $data = $request->post();

        $id = $data['id'];
        $o = $data['o'];

        if (empty($id) || empty($o)) {
            $res = array('info' => '缺少参数', 'status' => 0);
        } else {
            $AdminAuthRule = new AdminAuthRule();
            $result = $AdminAuthRule->setSort($id, $o);
            if ($result) {
                $res = array('data' => array('JumpUrl' => Url::build('index')), 'info' => '设置排序成功', 'status' => 1);
            } else {
                $res = array('info' => '设置排序失败', 'status' => 0);
            }
        }
        return json($res);
    }
}
