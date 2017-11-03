<?php
namespace app\common\controller;

use think\Controller;
use think\Request;

/**
 * 基础控制器
 * Class Base
 * @package app\common\controller
 */
class Base extends Controller
{
    /**
     * 控制器初始化
     * 在该控制器的方法调用之前首先执行
     *
     * @date 2017-09-05
     * @author HJM
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 空操作
     * 系统在找不到指定的操作方法的时候，会定位到空操作（_empty）方法来执行
     * 利用这个机制，我们可以实现错误页面和一些URL的优化
     *
     * @return string
     *
     * @date 2017-09-05
     * @author HJM
     */
    public function _empty()
    {
        $request = Request::instance();
        // 当前为POST请求或者Ajax请求时
        if ($request->isPost() || $request->isAjax())
            return json_encode([]);
        $this->assign('info', '没有“' . $request->action() . '”方法');
        return $this->fetch('common@Base/_empty');
    }
}
