<?php
namespace backend\demo\controller;

use think\Controller;

/**
 * 示例控制器
 * 顾名思义：开发参考例子，不能够给你们看:>
 * Class Example
 * @package app\index\controller
 */
class City extends Controller
{
    public function index()
    {
//        return $this->fetch();
        return $this->fetch('indexJquery');
    }
}