<?php
namespace app\common\model;

use think\Model;

class XzsSetting extends Model
{
    public function getSetting()
    {
//        return $this->column('k,v');
        return $this->column('v','k');
    }
}