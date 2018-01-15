<?php
namespace app\common\exception;

class ForbiddenException extends BaseException
{
    public $code=403;
    public $msg='权限不足';
    public $errorCode=10003;
}

