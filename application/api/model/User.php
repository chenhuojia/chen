<?php
namespace app\api\model;

class User extends BaseModel
{
    protected $autoWriteTimestamp = true;
    
    public function address(){
        return $this->hasOne('UserAddress','user_id','id');
    }
    
    /**
     * 通过openID查找用户
     * @param unknown $openid
     * **/
    public static function getByOpenID($openid){
        return self::where(['openid'=>['=',$openid]])->find();
    }
}

