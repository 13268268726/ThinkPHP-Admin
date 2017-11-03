<?php
namespace backend\index\controller;

use backend\common\controller\Base;
use think\Config;
use think\Db;
use think\Request;

class Index extends Base
{
    /**
     * 首页
     *
     * @date 2017-09-07
     * @author HJM
     */
    public function index()
    {
        $version = 'ZZT_0.2.1';
        $info = [
            '版本信息' => [
                'PHP版本' => PHP_VERSION,
                'MySQL版本' => self::_mysqlVersion(),
                'ThinkPHP版本' => THINK_VERSION,
                '当前后台版本' => $version,
            ],
            '服务器信息' => [
                '操作系统' => PHP_OS,
                '运行环境' => $_SERVER["SERVER_SOFTWARE"],
                '主机名' => $_SERVER['SERVER_NAME'],
                '服务器域名/IP' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            ],
            '通讯信息' => [
                'PHP运行方式' => php_sapi_name(),
                'WEB服务端口' => $_SERVER['SERVER_PORT'],
                '通信协议' => $_SERVER['SERVER_PROTOCOL'],
                '请求方法' => $_SERVER['REQUEST_METHOD'],
                '用户的IP地址' => Request::instance()->ip(0, true),
                '数据库占用空间' => self::_mysqlDbSize(),
                '剩余空间' => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            ],
            '网站信息' => [
                '网站文档目录' => $_SERVER["DOCUMENT_ROOT"],
                '浏览器信息' => substr($_SERVER['HTTP_USER_AGENT'], 0, 40),
                '上传附件限制' => ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled",
                '执行时间限制' => ini_get('max_execution_time') . '秒',
                '服务器时间' => date("Y年n月j日 H:i:s"),
                '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            ]
        ];

        $this->assign('info', $info);
        $this->assign('version', $version);
        return $this->fetch();
    }


    /**
     * 数据库版本
     * @return mixed
     *
     * @date 2017-09-07
     * @author HJM
     */
    private static function _mysqlVersion()
    {
        $version = Db::query("select version() as ver");
        return $version[0]['ver'];
    }

    /**
     * 数据库占用空间
     * @return string
     *
     * @date 2017-09-07
     * @author HJM
     */
    private static function _mysqlDbSize()
    {
        $sql = "SHOW TABLE STATUS FROM " . Config::get('database.database');
        $tblPrefix = Config::get('database.prefix');
        if ($tblPrefix != null) {
            $sql .= " LIKE '{$tblPrefix}%'";
        }
        $row = Db::query($sql);
        $size = 0;
        foreach ($row as $val) {
            $size += $val["Data_length"] + $val["Index_length"];
        }
        return round(($size / 1048576), 2) . 'M';
    }
}
