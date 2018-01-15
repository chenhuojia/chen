<?php
namespace app\api\model;

use think\Model;

class BannerItem extends Model
{
    protected $hidden = ['id', 'img_id', 'banner_id', 'delete_time','update_time'];
    
    /**
     * 关联Banner 图片
     * @return \think\model\relation\BelongsTo
     * ***/
    public function img(){
        return $this->belongsTo('Image','img_id','id');
    }
    
}

