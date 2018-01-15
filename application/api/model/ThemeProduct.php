<?php
namespace app\api\model;

use think\Model;

class ThemeProduct extends Model
{
    public function products(){
        return $this->belongsTo('Product','product_id','id');
    }
   
}

