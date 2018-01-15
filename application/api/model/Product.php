<?php
namespace app\api\model;

use app\api\model\BaseModel;

class Product  extends BaseModel
{

    protected $hidden=[
       'create_time','delete_time','update_time','pivot','from','img_id'
    ];
    
    public function getMainImgUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }
    
    
    public function imgs(){
        return $this->hasMany('ProductImage','product_id','id');
    }
    
    public function properite(){
        return $this->hasMany('ProductProperty','product_id','id');
    }
    
    /**
     * 商品分页
     * @param unknown $count
     * @param number $skip
     * ***/
    public static function getMostRecent($count,$skip=0){
        return $product=self::limit($skip,$count)->order('id desc')->select();
    }
    
    
    /**
     * 获取分类产品
     * @param unknown $id
     * ***/
    public static function getCategoryProducts($id,$skip=0,$take=15){
        return $products=self::where(['category_id'=>['=',$id]])->limit($skip,$take)->order('id desc')->select();
    }
    
    /**
     * 获取商品详情
     * @param unknown $id
     * @return unknown
     * ***/
    public static function getProductDetial($id){
        $product=self::with([
            'imgs'=>function($query){
                $query->with('imgUrl')->order('order','desc');
                }
            ])
        ->with(['properite'])
        ->find($id);
        return $product;
    }
}

