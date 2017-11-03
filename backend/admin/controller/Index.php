<?php
namespace backend\admin\controller;

use think\Controller;

class Index extends Controller
{
    /**
     * 首页
     *
     * @date 2017-09-07
     * @author HJM
     */
    public function index()
    {
        return $this->fetch();
    }
}
