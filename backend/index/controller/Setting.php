<?php
namespace backend\index\controller;

use backend\common\controller\Base;
use backend\index\model\XzsSetting;
use think\Request;
use think\Url;

class Setting extends Base
{
    static private $table = 'xzs_setting';

    /**
     * 显示资源列表
     *
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-06-20
     */
    public function index()
    {
        $list = XzsSetting::all();
        if (!empty($list)) {
            $this->assign('list', $list);
        }
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-06-20
     */
    public function create()
    {
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\response\Json
     *
     * @author HJM
     * @date 2017-06-20
     */
    public function save(Request $request)
    {
        is_ajax();
        $data = $request->post();

        $validate = $this->validate($data, 'XzsSetting.columnSet');
        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $XzsSetting = new XzsSetting;
            $result = $XzsSetting->allowField(true)->save($data);
            if ($result) {
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '添加配置成功', 'status' => 1];
            } else {
                $res = ['info' => '添加配置失败', 'status' => 0];
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
     * @date 2017-06-20
     */
    public function edit($id)
    {
        $field = XzsSetting::get($id);
        $this->assign('field', $field);
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\response\Json
     *
     * @author HJM
     * @date 2017-06-20
     */
    public function update(Request $request, $id)
    {
        is_ajax();
        $data = $request->post();
        $validate = $this->validate($data, 'XzsSetting.columnSet');
        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $XzsSetting = new XzsSetting;
            $result = $XzsSetting->allowField(true)->save($data, ['id' => $data['id']]);
            if ($result) {
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '修改配置成功', 'status' => 1];
            } else {
                $res = ['info' => '修改配置失败', 'status' => 0];
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
     * @date 2017-06-20
     */
    public function delete($id)
    {
        is_ajax();
        $result = XzsSetting::destroy($id);
        if ($result) {
            $res = ['info' => '删除配置成功', 'status' => 1];
        } else {
            $res = ['info' => '删除配置失败', 'status' => 0];
        }
        return json($res);
    }

    /**
     * 网站基础信息更新接口
     *
     * @param  \think\Request $request
     * @return \think\Response
     *
     * @author HJM
     * @date 2017-06-20
     */
    public function updateApi(Request $request)
    {
        is_ajax();
        $data = $request->post();
        $validate = $this->validate($data, 'XzsSetting.valueSet');
        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $XzsSetting = new XzsSetting;
            $result = $XzsSetting->allowField(true)->save($data, ['id' => $data['id']]);
            if ($result) {
                $res = ['info' => '更新配置成功', 'status' => 1];
            } else {
                $res = ['info' => '更新配置失败', 'status' => 0];
            }
        }
        return json($res);
    }
}
