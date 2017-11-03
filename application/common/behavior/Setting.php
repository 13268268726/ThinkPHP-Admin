<?php
namespace app\common\behavior;

use app\common\model\XzsSetting;
use think\Cache;

class Setting
{
    public function run(&$params)
    {
        $cacheName = 'Setting';
        $data = Cache::get($cacheName);
        if(empty($data)){
            $model = new XzsSetting();
            $data = $model->getSetting();

            Cache::set($cacheName,$data);
        }
    }
}