<?php
namespace backend\common\widget;

use backend\common\model\AdminAuthGroup;
use backend\common\model\AdminAuthRule;
use org\ArrFunc;
use think\Request;
use think\Session;
use think\View;

class Layout
{
    private $modules = [
        'admin' => ['class' => 'light-green', 'ruleId' => 1],
        'index' => ['class' => 'green', 'ruleId' => 2],
        'jia' => ['class' => 'blue', 'ruleId' => 3],
        'crm' => ['class' => 'orange', 'ruleId' => 4]
    ];

    /**
     * 头部页面布局
     * @return array
     */
    public function header()
    {
        $AdminAuthRule = new AdminAuthRule();
        $nav = $AdminAuthRule->getTopParent();

        $classArr = ['light-green', 'green', 'blue', 'orange'];

        $view = new View();
        $view->assign([
            'nav' => $nav,
            'classArr' => $classArr
        ]);
        return $view->fetch(APP_PATH . '/common/widget/tpl/layout/header.html');
    }

    /**
     * 页面面包屑
     * @param array $crumbs
     * @return string
     */
    public function breadcrumbs($crumbs = [])
    {
        if (empty($crumbs) || !is_array($crumbs)) {
            $arr = self::_currentMenuInfo(0);

            if ($arr) {
                foreach ($arr as $val) {
                    $crumbs[] = $val['title'];
                }
            } else {
                $crumbs = ['其他页面'];
            }
        }
        $view = new View();
        $view->assign('crumbs', array_reverse($crumbs));
        return $view->fetch(APP_PATH . '/common/widget/tpl/layout/breadcrumbs.html');
    }

    /**
     * 页面标题
     * @param string $tit
     * @return string
     */
    public function pageHeader($tit)
    {
        if (empty($tit)) {
            return '';
        }
        $view = new View();
        $view->assign('tit', $tit);
        return $view->fetch(APP_PATH . '/common/widget/tpl/layout/pageHeader.html');
    }

    /**
     * 左侧栏目页面布局
     * @return string
     */
    public function left()
    {
        $admin_id = Session::get('admin.admin_id');

        $AdminAuthRule = new AdminAuthRule();
        $where = ['status' => 1, 'is_link' => 1];
        if ($admin_id == 1) {
            $menuData = $AdminAuthRule->getTreeData($where, 'level');
        } else {
            // 获取管理员分组信息
            $AdminAuthGroup = new AdminAuthGroup();
            $data = $AdminAuthGroup->getAdminGroupInfo($admin_id);

            // 整理分组权限ID
            $rulesIdArr = [];
            if ($data) {
                foreach ($data as $val) {
                    if ($val) {
                        $arr = explode(',', $val['rules']);
                        $rulesIdArr = array_merge($rulesIdArr, array_filter($arr));
                    }
                }
            }

            // 根据权限ID获得权限信息
            $where['id'] = ['in', array_unique($rulesIdArr)];
            $menuData = $AdminAuthRule->getTreeData($where, 'level');
        }

        return $this->getLeftHtml($menuData);
    }

    /**
     * 获取右边栏目的html信息
     *
     * @param array $menuData
     * @return string
     */
    public function getLeftHtml($menuData)
    {
        $html = '';
        $module = Request::instance()->module();
        $modules = $this->modules;
        if (isset($modules[$module])) {
            $ruleId = $modules[$module]['ruleId'];
            if (isset($menuData[$ruleId])) {
                $html = self::_menuHtml($menuData[$ruleId]);
            }
        }
        return $html;
    }

    /**
     * 获取侧边栏目html代码
     *
     * @param array $menu 多叉树栏目数组
     * @return string
     *
     * @date:2017-06-27
     * @author:HJM
     */
    private static function _menuHtml($menu)
    {
        $str = '<ul class="nav nav-list">'; // 存储树状html代码

        // 当前操作的请求
        $request = Request::instance();
        $mca = $request->module() . '/' . $request->controller() . '/' . $request->action();
        $CurArr = self::_currentMenuInfo();

        foreach ($menu['_data'] as $val) {
            $url = (empty($val['name']) || $val['name'] == '#') ? '#' : url($val['name']);

            // 当前栏目class名
            $class = ($val['id'] == $CurArr['current']) ? ' class="active"' : (in_array($val['id'], $CurArr['parents']) ? ' class="active open"' : '');

            if (isset($val['_data']) && !empty($val['_data'])) { // 有子栏目
                $str .= sprintf('<li%s><a href="%s" class="dropdown-toggle"> <i class="menu-icon %s"></i>
                        <span class="menu-text"> %s </span>
                        <b class="arrow icon-angle-down"></b>
                    </a>', $class, $url, $val['icon'], $val['title']);
                $str .= self::_menuChildHtml($val['_data'], '', $mca, $CurArr);
            } else { // 没子栏目
                $str .= sprintf('<li%s><a href="%s"><i class="menu-icon %s"></i> <span class="menu-text"> %s </span> </a></li>', $class, $url, $val['icon'], $val['title']);
            }
        }
        $str .= '</ul>';
        return $str;
    }

    /**
     * 获取当前路径的(模块/控制器/方法)ID & 父级栏目ID
     *
     * @param int $isFn 是否处理
     * @return array
     *
     * @date:2017-06-28
     * @author:HJM
     */
    static private function _currentMenuInfo($isFn = 1)
    {
        $AdminAuthRule = new AdminAuthRule();
        $tree = $AdminAuthRule->getTreeData(['status' => 1], 'tree');
        $RuleList = $AdminAuthRule->getAllList(); // 栏目列表

        // 当前栏目ID
        $request = Request::instance();
        $module = $request->module();
        $controller = $request->controller();

        $mca = $module . '/' . $controller . '/' . $request->action();
        $PMca = $module . '/' . $controller . '/index';

        $CurID = isset($RuleList[$mca]) ? $RuleList[$mca] : (isset($RuleList[$PMca]) ? $RuleList[$PMca] : '');

        $ParArr = ArrFunc::parentChannel($tree, $CurID, 'id');

        if ($isFn != 1) {
            return $ParArr;
        }

        $arr = [];
        if (!empty($ParArr)) {
            foreach ($ParArr as $v) {
                if ($CurID == $v['id']) continue;
                array_push($arr, $v['id']);
            }
        }
        return array(
            'parents' => $arr,
            'current' => $CurID
        );
    }

    /**
     * 获取侧边栏目的子栏目html
     *
     * @param array $MenuChild
     * @param string $str
     * @param string $mca 当前模块/控制器/方法
     * @param array $CurArr 当前栏目信息
     * @return string
     *
     * @date:2017-06-27
     * @author:HJM
     */
    static private function _menuChildHtml($MenuChild, $str, $mca, $CurArr)
    {
        $str .= '<ul class="submenu">';
        foreach ($MenuChild as $k => $vo) {

            // 栏目class名
            $class = ($vo['id'] == $CurArr['current']) ? ' class="active"' : (in_array($vo['id'], $CurArr['parents']) ? '  class="active open"' : '');

            $url = (empty($vo['name']) || $vo['name'] == '#') ? '#' : url($vo['name']);

            // 当前栏目class名
            if (isset($vo['_data']) && !empty($vo['_data'])) { // 有子栏目
                $str .= sprintf('<li%s><a href="%s" class="dropdown-toggle"><i class="menu-icon %s"></i>
                        <span class="menu-text"> %s </span>
                        <b class="arrow icon-angle-down"></b></a>', $class, $url, $vo['icon'], $vo['title']);
                $str = self::_menuChildHtml($vo['_data'], $str, $mca, $CurArr);
                $str .= '</li>';
            } else { // 没子栏目
                $str .= sprintf('<li%s><a href="%s"><i class="%s"></i> %s </a></li>', $class, $url, $vo['icon'], $vo['title']);
            }
        }
        $str .= '</ul>';
        return $str;
    }


}