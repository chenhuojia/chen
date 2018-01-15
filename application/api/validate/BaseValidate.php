<?php
namespace app\api\validate;
use think\Validate;
use app\common\exception\ParmaeterException;

class BaseValidate extends Validate
{
    
    /*
     * 获取请求参数 进行集体校验
     * @throws Exception
     * @return boolean
     * ***/
    public  function goCheck(){       
        $params=request()->param();
        if ($result=$this->batch()->check($params)){
            return true;
        }
        throw new ParmaeterException([
            // $this->error有一个问题，并不是一定返回数组，需要判断
           'msg' => is_array($this->error)?implode(';', $this->error) : $this->error,
        ]); 
    }
    
    /**
     * 自定义验证是否为正整数
     * @param unknown $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return boolean|string
     * ***/
    protected function isPositiveInteger($value, $rule='', $data='', $field='')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return false;
    }
    
    /**
     * 自动义验证 空
     * @param unknown $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return string|boolean
     * ***/
    protected function isNotEmpty($value, $rule='', $data='', $field='')
    {
        if (empty($value)) {
            return false;
        } else {
            return true;
        }
    }
    
    //没有使用TP的正则验证，集中在一处方便以后修改
    //不推荐使用正则，因为复用性太差
    //手机号的验证规则
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 过滤参数
     * @param unknown $array
     * @throws ParmaeterException
     * @return unknown[]
     * ***/
    public function getDataByRule($array){
        if (array_key_exists('uid', $array)|| array_key_exists('user_id', $array)){
            throw new ParmaeterException(['msg'=>'参数名中含有非分参数名user_id或uid']);
        }
        $newArray=[];
        foreach ($this->rule as $k=>$v){
            $newArray[$k]=$array[$k];
        }
        return $newArray;
    }
    
}

