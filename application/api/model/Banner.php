<?php
namespace app\api\model;

use think\Model;

class Banner extends Model
{   
    
    protected $hidden=['id','delete_time','update_time'];
    
    /**
     * 关联BannerItem表
     * @return \think\model\relation\HasMany
     * ***/
    public function items(){
        return $this->hasMany('BannerItem','banner_id','id')->field('id,banner_id,type,img_id');
    }
    
    public static function getBannerId($id){
        return self::with(['items','items.img'])->field('id,name,description')->find($id); 
    }
    
}


