<?php
namespace app\common\exception;

class OrderException extends BaseException
{
    public $code=404;
    public $msg="订单信息有误";
    public $errorCode=10008;
    
}

