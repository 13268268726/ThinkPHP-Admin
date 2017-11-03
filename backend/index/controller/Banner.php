<?php
namespace backend\index\controller;

use backend\common\controller\Base;
use backend\index\model\XzsBanner;
use think\Request;
use think\Url;

class Banner extends Base
{
    static private $table = 'xzs_banner';

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $banner_name = Request::instance()->get('banner_name');
        $w = $banner_name ? ['banner_name' => ['like', "%$banner_name%"]] : [];
        $order = get_order('banner_sort asc');

        $XzsBanner = new XzsBanner();
        $list = $XzsBanner->getAllData($w, $order);

        $this->assign([
            'banner_name' => $banner_name,
            'list' => $list
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
     */
    public function save(Request $request)
    {
        is_post();
        $data = $request->post();

        //  图片上传
        $upload = self::_upload();
        if ($upload['status'] != 1) {
            $this->error($upload['info']);
        }
        $data['banner_url'] = $upload['data'];

        $XzsBanner = new XzsBanner();
        $validate = $this->validate($data, 'XzsBanner');

        if (true !== $validate) {
            $this->error($validate);
        } else {
            $result = $XzsBanner->allowField(true)->save($data);
            if ($result) {
                $this->log(self::$table, $XzsBanner->banner_id);
                $this->success('添加广告图成功', 'index');
            } else {
                $this->error('添加广告图失败');
            }
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $banner_id
     * @return \think\Response
     */
    public function edit($banner_id)
    {
        $field = XzsBanner::get($banner_id);
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
     * @param  int $banner_id
     */
    public function update(Request $request, $banner_id)
    {
        is_post();
        $data = $request->post();

        //  图片上传
        if (!empty($_FILES['banner_img']['name']) && $_FILES['banner_img']['error'] == 0) {
            $upload = self::_upload();
            if ($upload['status'] != 1) {
                $this->error($upload['info']);
            }
            $data['banner_url'] = $upload['data'];
        }

        $XzsBanner = new XzsBanner();
        $validate = $this->validate($data, 'XzsBanner');

        if (true !== $validate) {
            $this->error($validate);
        } else {
            $result = $XzsBanner->allowField(true)->save($data, ['banner_id' => $data['banner_id']]);
            if ($result) {
                $this->log(self::$table, $banner_id);
                $this->success('修改广告图成功', 'index');
            } else {
                $this->error('修改广告图失败');
            }
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $banner_id
     * @return \think\Response
     */
    public function delete($banner_id)
    {
        is_ajax();
        $result = XzsBanner::destroy($banner_id);

        if ($result) {
            $this->log(self::$table, $banner_id);
            $res = ['info' => '删除广告图成功', 'status' => 1];
        } else {
            $res = ['info' => '删除广告图失败', 'status' => 0];
        }
        return json($res);
    }

    /**
     * 批量设置排序接口
     *
     * @author:HJM
     * @date:2017-07-05
     */
    public function sortApi()
    {
        is_ajax();

        $data = Request::instance()->post();

        $banner_id = $data['banner_id'];
        $banner_sort = $data['banner_sort'];

        if (empty($banner_id) || empty($banner_sort)) {
            $res = ['info' => '缺少参数', 'status' => 0];
        } else {

            $XzsBanner = new XzsBanner();
            $result = $XzsBanner->setSort($banner_id, $banner_sort);
            if ($result) {
                $this->log(self::$table, 0);
                $res = ['data' => ['JumpUrl' => Url::build('index')], 'info' => '设置排序成功', 'status' => 1];
            } else {
                $res = ['info' => '设置排序失败', 'status' => 0];
            }
        }
        return json($res);
    }

    /**
     * 图片上传
     *
     * @return array
     */
    private function _upload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('banner_img');

        if($file == null){
            return array(
                'info' => '请选择图片',
                'status' => 0,
            );
        }

        // 移动到框架应用根目录/public/uploads/banner/ 目录下
        $path = DS . 'uploads' . DS . 'banner';
        $info = $file
            ->validate(['size' => 3145728, 'ext' => 'jpg,jpeg,png,gif'])
            ->move(ROOT_PATH . 'public' . $path);

        if ($info) {
            // 成功上传后 获取上传信息
            $res = [
                'data' => $path . DS . $info->getSaveName(),
                'info' => '上传成功',
                'status' => 1,
            ];
        } else {
            // 上传失败获取错误信息
            $res = [
                'info' => $file->getError(),
                'status' => 0,
            ];
        }
        return $res;
    }
}
