<?php
namespace app\common\exception;

use app\common\exception\BaseException;

class TokenException extends BaseException
{
    public $code=401;
    public $msg='token有误或已过期';
    public $errorCode=10000;
    
    
}

