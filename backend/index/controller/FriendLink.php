<?php
namespace backend\index\controller;

use backend\common\controller\Base;
use backend\index\model\XzsFriendlyLink;
use think\Request;
use think\Url;

class FriendLink extends Base
{
    static private $table = 'xzs_friendly_link';

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $friend_name = Request::instance()->get('friend_name');
        $w = $friend_name ? array('friend_name' => array('like', "%$friend_name%")) : [];
        $order = get_order('friend_sort asc');

        $XzsFriendlyLink = new XzsFriendlyLink();
        $list = $XzsFriendlyLink->getAllData($w, $order);

        $this->assign([
            'friend_name' => $friend_name,
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
        $data['friend_img'] = $upload['data'];

        $XzsFriendlyLink = new XzsFriendlyLink();
        $validate = $this->validate($data, 'XzsFriendlyLink');

        if (true !== $validate) {
            $this->error($validate);
        } else {
            $result = $XzsFriendlyLink->allowField(true)->save($data);
            if ($result) {
                $this->log(self::$table, $XzsFriendlyLink->friend_id);
                $this->success('添加友情链接成功', 'index');
            } else {
                $this->error('添加友情链接失败');
            }
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $friend_id
     * @return \think\Response
     */
    public function edit($friend_id)
    {
        $field = XzsFriendlyLink::get($friend_id);
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
     * @param  int $friend_id
     */
    public function update(Request $request, $friend_id)
    {
        is_post();
        $data = $request->post();

        //  图片上传
        if (!empty($_FILES['friend_img_pic']['name']) && $_FILES['friend_img_pic']['error'] == 0) {
            $upload = self::_upload();
            if ($upload['status'] != 1) {
                $this->error($upload['info']);
            }
            $data['friend_url'] = $upload['data'];
        }

        $XzsFriendlyLink = new XzsFriendlyLink();
        $validate = $this->validate($data, 'XzsFriendlyLink');

        if (true !== $validate) {
            $this->error($validate);
        } else {
            $result = $XzsFriendlyLink->allowField(true)->save($data, ['friend_id' => $data['friend_id']]);
            if ($result) {
                $this->log(self::$table, $friend_id);
                $this->success('修改友情链接成功', 'index');
            } else {
                $this->error('修改友情链接失败');
            }
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $friend_id
     * @return \think\Response
     */
    public function delete($friend_id)
    {
        is_ajax();
        $result = XzsFriendlyLink::destroy($friend_id);

        if ($result) {
            $this->log(self::$table, $friend_id);
            $res = ['info' => '删除友情链接成功', 'status' => 1];
        } else {
            $res = ['info' => '删除友情链接失败', 'status' => 0];
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

        $friend_id = $data['friend_id'];
        $friend_sort = $data['friend_sort'];

        if (empty($friend_id) || empty($friend_sort)) {
            $res = ['info' => '缺少参数', 'status' => 0];
        } else {
            $XzsFriendlyLink = new XzsFriendlyLink();
            $result = $XzsFriendlyLink->setSort($friend_id, $friend_sort);
            if ($result) {
                $this->log(self::$table, 0);
                $res = ['data' => array('JumpUrl' => Url::build('index')), 'info' => '设置排序成功', 'status' => 1];
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
        $file = request()->file('friend_img_pic');

        if($file == null){
            return array(
                'info' => '请选择图片',
                'status' => 0,
            );
        }

        // 移动到框架应用根目录/public/uploads/friendlink/ 目录下
        $path = DS . 'uploads' . DS . 'friendlink';
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
}
