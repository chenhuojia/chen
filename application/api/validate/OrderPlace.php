<?php
namespace app\api\validate;

use app\common\exception\ParmaeterException;

class OrderPlace extends BaseValidate
{
    protected $rule=[
       'products'=>'require|checkProducts'
    ];
    protected $singleRule=[
        'product_id'=>'require|isPositiveInteger',
        'count'=>'require|isPositiveInteger',
    ];
    
    protected function checkProducts($values){        
        if (!is_array($values)){
            throw new ParmaeterException([
                'msg'=>'商品参数不正确',
            ]);
        }
        if(empty($values)){
            throw new ParmaeterException([
                'msg'=>'商品列表不能为空',
            ]);
        }
        foreach ($values as $value)
        {
            $this->checkProduct($value);
        }
        return true;
    }
    
    
    private function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if(!$result){
            throw new ParmaeterException([
                'msg' => '商品列表参数错误',
            ]);
        }
    }
}

