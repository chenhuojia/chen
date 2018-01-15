<?php
namespace app\api\behavior;

use think\Log;

class SqlBehavior 
{
    
    public function run(){
           Log::init([
               'type' => 'File',
               //日志保存目录
               'path' => LOG_PATH,
               //单个日志文件的大小限制，超过后会自动记录到第二个文件
               'file_size'     =>2097152,
               //日志的时间格式，默认是` c `
               'time_format'   =>'c',
               'level' => ['sql'],
           ]);
           Log::info('sql日志信息');
    }
    
}

