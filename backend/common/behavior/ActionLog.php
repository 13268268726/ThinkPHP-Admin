<?php
namespace backend\common\behavior;

use think\Db;
use think\Request;
use think\Session;

class ActionLog
{
    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    public static function init()
    {
        define('NOW_TIME', $_SERVER['REQUEST_TIME']);
    }

    /**
     * 记录管理员操作事件
     * @param array $params
     *
     * @date:2017-07-04
     * @author:HJM
     */
    public function run(&$params)
    {
        self::init();

        // 当前操作的请求
        $request = Request::instance();
        $mca = strtolower($request->module()) . '/' . strtolower($request->controller()) . '/' . strtolower($request->action());

        $adminId = Session::get('admin.admin_id');
        $adminName = Session::get('admin.admin_name');
        $this->keepLog($mca, $params['model'], $params['record_id'], $adminId, $adminName);
    }

    /**
     * 记录行为日志，并执行该行为的规则
     *
     * @param string $action 行为标识
     * @param string $model 触发行为的模型名
     * @param int $record_id 触发行为的记录id
     * @param int $user_id 执行行为的用户id
     * @param string $user_name 执行行为的用户名称
     * @return string
     *
     * @date:2017-07-04
     * @author:HJM
     */
    public function keepLog($action = null, $model = null, $record_id = null, $user_id = null, $user_name = null)
    {
        // 参数检查
        if (empty($action) || empty($model) || empty($record_id)) {
            return '参数不能为空';
        }

        //查询行为,判断是否执行
        $ActionInfo = Db::name('AdminAuthRule')->where('name', $action)->find();
        if ($ActionInfo['status'] != 1) {
            return '该行为被禁用或删除';
        }

        //插入行为日志
        $data['action_id'] = $ActionInfo['id'];
        $data['user_id'] = $user_id;
        $data['action_ip'] = ip2long(Request::instance()->ip());
        $data['model'] = $model;
        $data['record_id'] = $record_id;
        $data['create_time'] = NOW_TIME;

        //解析日志规则，生成日志备注
        if (!empty($ActionInfo['log'])) {
            if (preg_match_all('/\[(\S+?)\]/', $ActionInfo['log'], $match)) {
                $replace = [];
                $log['user_id'] = $user_id;
                $log['user'] = $user_name;
                $log['record'] = $record_id;
                $log['model'] = $model;
                $log['time'] = NOW_TIME;
                $log['data'] = ['user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => NOW_TIME];
                foreach ($match[1] as $value) {
                    $param = explode('|', $value);
                    if (isset($param[1])) {
                        $replace[] = call_user_func($param[1], $log[$param[0]]);
                    } else {
                        $replace[] = $log[$param[0]];
                    }
                }
                $data['remark'] = str_replace($match[0], $replace, $ActionInfo['log']);
            } else {
                $data['remark'] = $ActionInfo['log'];
            }
        } else {
            // 未定义日志规则，记录操作url
            $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
        }

        Db::name('AdminActionLog')->insert($data);

        if (!empty($ActionInfo['rule'])) {
            //解析行为
            $rules = $this->_parseAction($action, $user_id);

            //执行行为
            $this->_executeAction($rules, $ActionInfo['id'], $user_id);
        }
        return '';
    }

    /**
     * 解析行为规则
     * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
     * 规则字段解释：table->要操作的数据表，不需要加表前缀；
     *              field->要操作的字段；
     *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
     *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
     *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
     *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
     * 单个行为后可加；连接其他规则
     * @param string $action 行为id或者name
     * @param int $self 替换规则里的变量为执行用户的id
     * @return bool|array: false解析出错，成功返回规则数组
     *
     * @date:2017-07-04
     * @author:HJM
     */
    private function _parseAction($action = null, $self)
    {
        if (empty($action)) {
            return false;
        }

        //参数支持id或者name
        if (is_numeric($action)) {
            $map = ['id' => $action];
        } else {
            $map = ['name' => $action];
        }

        //查询行为信息
        $info = Db::name('AdminAuthRule')->where($map)->find();
        if (!$info || $info['status'] != 1) {
            return false;
        }

        //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
        $rules = $info['rule'];
        $rules = str_replace('{$self}', $self, $rules);
        $rules = explode(';', $rules);
        $return = [];
        foreach ($rules as $key => &$rule) {
            $rule = explode('|', $rule);
            foreach ($rule as $k => $fields) {
                $field = empty($fields) ? [] : explode(':', $fields);
                if (!empty($field)) {
                    $return[$key][$field[0]] = $field[1];
                }
            }
            //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
            if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
                unset($return[$key]['cycle'], $return[$key]['max']);
            }
        }
        return $return;
    }

    /**
     * 执行行为
     * @param bool|array $rules 解析后的规则数组
     * @param int $action_id 行为id
     * @param array $user_id 执行的用户id
     * @return bool
     *
     * @date:2017-07-04
     * @author:HJM
     */
    private function _executeAction($rules = false, $action_id = null, $user_id = null)
    {
        if (!$rules || empty($action_id) || empty($user_id)) {
            return false;
        }

        $return = true;
        foreach ($rules as $rule) {

            // 检查执行周期
            $map = ['action_id' => $action_id, 'user_id' => $user_id];
            $map['create_time'] = ['gt', NOW_TIME - intval($rule['cycle']) * 3600];
            $exec_count = Db::name('AdminActionLog')->where($map)->count();
            if ($exec_count > $rule['max']) {
                continue;
            }

            //执行数据库操作
            $Model = Db::name(ucfirst($rule['table']));
            $field = $rule['field'];
            $res = $Model->where($rule['condition'])->setField($field, ['exp', $rule['rule']]);

            if (!$res) {
                $return = false;
            }
        }
        return $return;
    }
}
