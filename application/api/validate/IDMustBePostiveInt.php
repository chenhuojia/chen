<?php
namespace app\api\validate;
use app\api\validate\BaseValidate;
class IDMustBePostiveInt extends BaseValidate
{
    protected $rule=[
        'id'=>'require|number|isPositiveInteger',
    ];
    
    protected $message=[
        'id'=>'id必须是正整数',
    ];
    

}

