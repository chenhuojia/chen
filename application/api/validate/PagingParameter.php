<?php
namespace app\api\validate;
use app\api\validate\BaseValidate;
class PagingParameter extends BaseValidate
{
    protected $rule=[
        'page'=>'number|isPositiveInteger',
        'size'=>'number|isPositiveInteger',
    ];
    protected $message=[
        'page'=>'page必须是正整数',
        'size'=>'size必须是正整数',
    ];
}

