<?php
namespace app\api\model;
use app\api\model\BaseModel;

class Image extends BaseModel{
    protected $hidden = ['delete_time','from','update_time','id'];
    public function getFromAttr($value)
    {
        $status = [1=>'来自本地',2=>'来自公网'];
        return $status[$value];
    }
    
    public function getUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }
    
    
}