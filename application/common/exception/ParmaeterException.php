<?php
namespace app\common\exception;
use app\common\exception\BaseException;
class ParmaeterException extends BaseException
{
    public $code=400;
    public $msg='参数错误';
    public $errorCode=10000;
    
}

