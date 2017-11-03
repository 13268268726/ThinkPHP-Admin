<?php

namespace backend\index\controller;

use backend\common\controller\Base;
use backend\index\model\XzsTeacher;
use think\Request;
use think\Url;

class Teacher extends Base
{
    static private $table = 'xzs_teacher';

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $name = Request::instance()->get('name');
        $w = $name ? array('name' => array('like', "%$name%")) : [];

        $XzsTeacher = new XzsTeacher();
        $list = $XzsTeacher->getPageData($w);

        $this->assign([
            'name' => '',
            'list' => $list,
            'PageInfo' => $list->render(),
        ]);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
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
     */
    public function save(Request $request)
    {
        is_post();
        $data = $request->post();

        $XzsTeacher = new XzsTeacher();
        $validate = $this->validate($data, 'XzsTeacher');

        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $result = $XzsTeacher->allowField(true)->save($data);
            if ($result) {
                $this->log(self::$table, $XzsTeacher->teacher_id);
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '添加讲师成功', 'status' => 1];
            } else {
                $res = ['info' => '添加讲师失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $teacher_id
     * @return \think\Response
     */
    public function edit($teacher_id)
    {
        $field = XzsTeacher::get($teacher_id);
        if ($field) {
            $this->assign('field', $field);
        } else {
            $this->error("没有图片资源");
        }
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $teacher_id
     * @return \think\Response
     */
    public function update(Request $request, $teacher_id)
    {
        is_post();
        $data = $request->post();

        $XzsTeacher = new XzsTeacher();
        $validate = $this->validate($data, 'XzsTeacher');

        if (true !== $validate) {
            $res = ['info' => $validate, 'status' => 0];
        } else {
            $result = $XzsTeacher->allowField(true)->save($data, ['teacher_id' => $teacher_id]);
            if ($result) {
                $this->log(self::$table, $teacher_id);
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '修改讲师成功', 'status' => 1];
            } else {
                $res = ['info' => '修改讲师失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 删除指定资源
     *
     * @param  int $teacher_id
     * @return \think\Response
     */
    public function delete($teacher_id)
    {
        is_ajax();
        $result = XzsTeacher::destroy($teacher_id);

        if ($result) {
            $this->log(self::$table, $teacher_id);
            $res = ['info' => '删除讲师成功', 'status' => 1];
        } else {
            $res = ['info' => '删除讲师失败', 'status' => 0];
        }
        return json($res);
    }

    /**
     * 图片上传接口
     *
     * @return array
     */
    public function upload()
    {
        // 获取表单上传文件
        $file = request()->file('upload_img');

        if ($file == null) {
            return array(
                'info' => '请选择图片',
                'status' => 0,
            );
        }

        // 移动到框架应用根目录/public/uploads/teacher/ 目录下
        $path = DS . 'uploads' . DS . 'teacher';
        $info = $file
            ->validate(['size' => 3145728, 'ext' => 'jpg,jpeg,png,gif'])
            ->move(ROOT_PATH . 'public' . $path);

        if ($info) {
            // 成功上传后 获取上传信息
            $res = array(
                'data' => $path . DS . $info->getSaveName(),
                'info' => '上传成功',
                'status' => 1,
            );
        } else {
            // 上传失败获取错误信息
            $res = array(
                'info' => $file->getError(),
                'status' => 0,
            );
        }
        return $res;
    }

    /**
     * 批量设置排序接口
     *
     * @return \think\Response
     */
    public function sortApi()
    {
        is_ajax();

        $data = Request::instance()->post();

        $teacher_id = $data['teacher_id'];
        $sort = $data['sort'];

        if (empty($teacher_id) || empty($sort)) {
            $res = ['info' => '缺少参数', 'status' => 0];
        } else {
            $XzsTeacher = new XzsTeacher();
            $result = $XzsTeacher->setSort($teacher_id, $sort);
            if ($result) {
                $this->log(self::$table, 0);
                $res = ['data' => array('JumpUrl' => Url::build('index')), 'info' => '设置排序成功', 'status' => 1];
            } else {
                $res = ['info' => '设置排序失败', 'status' => 0];
            }
        }
        return json($res);
    }
}
