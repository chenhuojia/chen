<?php
namespace app\api\controller\v1;

use app\api\validate\Count;
use app\api\model\Product;
use app\common\exception\MissException;
use app\api\validate\IDMustBePostiveInt;

class Product
{
    /**
     * 获取新的商品列表
     * @URL getNewProducts/[:skip]/[:count]
     * @param number $count
     * @param number $skip
     * @throws MissException
     * @return unknown
     * ***/
    public function getNewProducts($count=15,$skip=0){
        (new Count())->goCheck();
         $product=Product::getMostRecent($count,$skip);
         if ($product->isEmpty()){
             throw new MissException(['产品不存在']);
         }
         $product->hidden(['summary',]);
         return $product;
    }
   
    /**
     * 
     * @param unknown $id
     * @param number $skip
     * @param number $take
     * @throws MissException
     * @return unknown
     * ***/
    public function getCategoryProducts($id,$skip=0,$take=15){
        (new IDMustBePostiveInt())->goCheck();
        $product=Product::getCategoryProducts($id,$skip,$take);
        if ($product->isEmpty()){
            throw new MissException(['产品不存在']);
        }
        $product->hidden(['summary',]);
        return $product;
    }
    
    
    public function getProductOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $product=Product::getProductDetial($id);
        return $product;
    }
}

