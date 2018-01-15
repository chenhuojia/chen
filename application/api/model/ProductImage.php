<?php
namespace app\api\model;

class ProductImage extends BaseModel
{
        protected $hidden=[
            'create_time','delete_time','update_time','img_id'
        ];
    
       public function imgUrl(){
           return $this->belongsTo('Image','img_id','id');
       } 
    
}

