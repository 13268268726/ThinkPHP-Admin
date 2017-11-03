<?php
namespace app\common\widget;

use app\common\model\XzsNav;
use think\Cache;
use think\Request;
use think\View;

class Layout
{
    public function header($isHomePage = 0)
    {
        $NavModel = new XzsNav();
        $menu = $NavModel->getMenu();
        $view = new View();

        $view->assign([
            'menu'  => $menu,
            'setting' => Cache::get('Setting'),
        ]);

        if ($isHomePage) {
            return $view->fetch(APP_PATH . '/common/widget/tpl/layout/homePageHeader.html');
        } else {
            return $view->fetch(APP_PATH . '/common/widget/tpl/layout/header.html');
        }
    }
}