<?php
namespace backend\index\controller;

use backend\common\controller\Base;

/**
 * 空控制器
 * 当系统找不到指定的控制器名称的时候，系统会尝试定位空控制器(Error)
 * 利用这个机制我们可以用来定制错误页面和进行URL的优化
 *
 * Class Error
 * @package app\index\controller
 */
class Error extends Base
{
    /**
     * 指向默认报错页面
     *
     * @date 2017-09-05
     * @author HJM
     */
    public function index()
    {
        return $this->_empty();
    }
}
