<?php
namespace app\api\controller\v1;

use app\api\validate\TokenGet;
use app\api\service\UserToken;
use app\common\exception\TokenException;
use app\api\service\Token as TokenService;

class Token
{
    /**
     * 获取token
     * @param unknown $code
     * @return string[]
     * ***/
    public function getToken($code){
        (new TokenGet())->goCheck();
        $userToken=new UserToken($code);
        $token=$userToken->getToken();
        return ['token'=>$token];
    }
    
    public function verifyToken($token=''){
        if (!$token){
            throw  new TokenException(['msg'=>'Token不能为空']);
        }
        $valid = TokenService::verifyToken($token);
        return ['isValid'=>$valid];
    }
    
}

