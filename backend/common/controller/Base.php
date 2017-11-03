<?php
namespace backend\common\controller;

use think\Controller;
use think\Hook;
use think\Request;

/**
 * 后台系统基础控制器
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
        $this->redirect('index/master/_empty');
        return '没有“' . $request->action() . '”方法';
    }

    /**
     * 日志记录
     *
     * @param string $table
     * @param int $id
     *
     * @date:2017-09-08
     * @author:HJM
     */
    public static function log($table, $id)
    {
        $arr = array('model' => $table, 'record_id' => $id);
        Hook::exec('backend\\common\\behavior\\ActionLog', 'run', $arr);
    }
}
