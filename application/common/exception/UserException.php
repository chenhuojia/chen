<?php
namespace app\common\exception;

class UserException extends BaseException
{
    public $code=401;
    public $message="用户不存在";
    public $errorCode=10002;
}

