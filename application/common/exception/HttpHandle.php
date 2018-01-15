<?php
namespace app\common\exception;

use think\exception\Handle;
use think\Log;
class HttpHandle extends Handle
{
    private $code; //http 状态码
    private $msg;  //异常解析
    private $errorCode; //自定义错误码
    
    /*
     * 抛出异常
     * {@inheritDoc}
     * @see \think\exception\Handle::render()
     * ***/
    public function render(\Exception $e){   
        if ($e instanceof BaseException){
            $this->code=$e->code;
            $this->msg=$e->msg;
            $this->errorCode=$e->errorCode;
        }else{
            if (config('app_debug')){
                return parent::render($e);
            }
            $this->code=500;
            $this->msg='服务器内部错误';
            $this->errorCode=99999;
            $this->errorLog($e);
        }
        $data=[
            'msg'=>$this->msg,
            'error_code'=>$this->errorCode,
            'request_time'   =>  date('Y-d-m H:i:s',request()->time()),
            'request_url'    =>  request()->domain().request()->url(),
        ];
        return json($data, $this->code);            
    }
   
    private static function errorLog(\Exception $e){
        Log::init([
            'type'  =>  'File',
            'path'  =>  LOG_PATH,
            'apart_level'   =>  ['error','sql'],
            'level' => ['error']
        ]);
        return Log::record($e->getMessage(),'error');
    } 
    
}

