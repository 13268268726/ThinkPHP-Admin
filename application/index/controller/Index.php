<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\XzsBanner;
use app\index\model\XzsMovieType;
use app\index\model\XzsTeacher;
use app\index\model\XzsMovie;

class Index extends Base
{
    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        // 获取广播图数据
        $XzsBanner = new XzsBanner();
        $banner = $XzsBanner->getAllData();

        // 获取讲师数据
        $XzsTeacher = new XzsTeacher();
        $teacher = $XzsTeacher->getLimitData(12);

        // 视频栏目类型
        $XzsMovieType = new XzsMovieType();
        $mvType = $XzsMovieType->getTreeData('level');

        // 视频信息
        $mvInfo = [];
        $XzsMovie = new XzsMovie();
        foreach ($mvType as $val) {
            foreach ($val['_data'] as $k => $v) {
                $cruType = $v['mv_type_id'];
                $obj = $XzsMovie->where(['mv_type_id' => $cruType])->limit(8)->order('created_at desc')->select();
                $mvInfo[$val['mv_type_id']][$cruType] = $obj->toArray();
            }
        }
        $this->assign([
            'banner' => $banner,
            'teacher' => $teacher,
            'mvType' => $mvType,
            'mvInfo' => $mvInfo,
            'ariaControls' => ['home', 'profile', 'messages']
        ]);
        return $this->fetch();
    }

    public function aa()
    {

    }
}
