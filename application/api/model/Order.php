<?php
namespace app\api\model;
use app\api\model\BaseModel;
class Order extends BaseModel
{
 
   protected $autoWriteTimestamp = true;
   
   public function getSnapItemsAttr($value){
       if (empty($value)){
           return false;
       }
       return json_decode($value,true);
   }
   public function getSnapAddressAttr($value){
       if (empty($value)){
           return false;
       }
       return json_decode($value,true);
   }
   
   public static function getSummaryByUser($uid,$page=1,$size=15){
       $pageData=self::where('user_id','=',$uid)
       ->order('create_time desc')
       ->field('id,order_no,create_time,total_price,status,snap_img,snap_name,total_count,update_time')
       ->paginate($size,true,['page'=>$page]);
      if ($pageData->isEmpty()){
          return [
              'data'=>[],
              'current_page'=>$pageData->getCurrentPage(),
          ];
      }
      return [
          'data'=>$pageData->toArray(),
          'current_page'=>$pageData->getCurrentPage(),
      ];
   }
   
   
   
}

