<?php
namespace app\api\validate;

class IDCollection extends BaseValidate
{

    protected $rule=[
        'ids'=>'require|checkIDs'
    ];
    
    protected $message=[
        'ids'=>'ids必须为以逗号间隔的正整数',
    ];
    
    protected function checkIDs($value){
        if (!is_string($value)) return false;
        $value=explode(',', $value);
        if (empty($value)) return false;
        foreach ($value as $k=>$v){
           if (!$this->isPositiveInteger($v)){
               return false;
               break;
           }
        }
        return true;
    }
    
}

