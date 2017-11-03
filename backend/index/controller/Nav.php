<?php

namespace backend\index\controller;

use backend\common\controller\Base;
use backend\index\model\XzsNav;
use think\Request;
use think\Url;

class Nav extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-07-05
     */
    public function index()
    {
        $XzsNav = new XzsNav();
        $data = $XzsNav->getTreeData('tree', 'nav_sort asc');
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 表单接收，分配方法
     *
     * @author HJM
     * @date 2017-07-05
     */
    public function formFnApi()
    {
        is_ajax();
        $form_type = Request::instance()->post('form_type');

        switch ($form_type) {
            case 1: // 添加
            case 2: // 添加子类
                return $this->_addApi();
                break;
            case 3: // 修改
                return $this->_editApi();
                break;
        }
        return json(['info' => '数据类型错误', 'status' => 0]);
    }

    /**
     * 添加导航栏
     *
     * @author HJM
     * @date 2017-07-05
     */
    private function _addApi()
    {
        $data = Request::instance()->post();

        $validate = $this->validate($data, 'XzsNav');
        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $XzsNav = new XzsNav($data);
            $result = $XzsNav->allowField(true)->save();

            if ($result) {
//                $XzsNav->updateBookType($data);
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '添加导航栏成功', 'status' => 1];
            } else {
                $res = ['info' => '添加导航栏失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 修改导航栏
     *
     * @author HJM
     * @date 2017-07-05
     */
    private function _editApi()
    {
        $data = Request::instance()->post();
        $XzsNav = new XzsNav();

        $validate = $this->validate($data, 'XzsNav');
        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $result = $XzsNav->allowField(true)->save($data, ['nav_id' => $data['nav_id']]);
            if ($result) {
//                $XzsNav->updateBookType($data);
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '修改导航栏成功', 'status' => 1];
            } else {
                $res = ['info' => '修改导航栏失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 修改
     *
     * @author HJM
     * @date 2017-07-05
     */
    public function editAjax()
    {
        is_ajax();
        $nav_id = Request::instance()->post('nav_id');

        $data = XzsNav::get($nav_id)->toArray();
        if (empty($data)) {
            $res = ['info' => '该条记录可能已经被删除', 'status' => 0];
        } else {
            $res = ['data' => $data, 'info' => 'success', 'status' => 1];
        }
        return json($res);
    }


    /**
     * 批量设置排序接口
     *
     * @author HJM
     * @date 2017-07-05
     */
    public function sortApi()
    {
        is_ajax();

        $data = Request::instance()->post();
        $nav_id = $data['nav_id'];
        $nav_sort = $data['nav_sort'];

        if (empty($nav_id) || empty($nav_sort)) {
            $res = ['info' => '缺少参数', 'status' => 0];
        } else {
            $XzsNav = new XzsNav();
            $result = $XzsNav->setSort($nav_id, $nav_sort);
            if ($result) {
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '设置排序成功', 'status' => 1];
            } else {
                $res = ['info' => '设置排序失败', 'status' => 0];
            }
        }
        return json($res);
    }
}
