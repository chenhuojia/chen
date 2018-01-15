<?php
namespace app\common\exception;

use app\common\exception\BaseException;

class MissException extends BaseException
{
    public $code=404;
    public $msg='未找到资源';
    public $errorCode=10001;
}

