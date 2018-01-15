<?php
namespace app\common\exception;

use think\Exception;

class BaseException extends Exception
{   
    //http 状态码
    public $code=400;
    //异常解析
    public $msg='参数错误';
    //自定义错误码
    public $errorCode=10000; 
    
    public function __construct($data=[]){
        if (!is_array($data)) return;
        if (array_key_exists('code', $data)){
            $this->code=$data['code'];
        }
        if (array_key_exists('msg', $data)){
            $this->msg=$data['msg'];
        }
        if (array_key_exists('errorCode', $data)){
            $this->errorCode=$data['errorCode'];
        }
    }
}

