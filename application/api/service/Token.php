<?php
namespace app\api\service;
use app\common\exception\TokenException;
use think\Exception;
use app\common\exception\ForbiddenException;
use think\Cache;

class Token
{
    /**
     * 生成token
     * @return string
     * ***/
    public static function generateToken(){
        $randChars=getRandChar();
        $timestamp=$_SERVER['REQUEST_TIME_FLOAT'];
        $salt=config('secure.token_salt');
        return md5($randChars.$timestamp.$salt);
    }
    
    /**
     * 获取当前token缓存值
     * @param unknown $key
     * @throws TokenException
     * @throws Exception
     * @return mixed
     * ***/
    public static function getCurrentTokenVar($key){
        $token=request()->header('token');
        $vars=\think\Cache::get($token);
        if (empty($vars)){
            throw new TokenException();
        }
        if (!is_array($vars)) $vars=json_decode($vars,true);
        if (!array_key_exists($key,$vars)){
            throw new Exception('该Token已失效');
        } 
        return $vars[$key];
    }
    
    /**
     * 获取当前用户ID
     * @return mixed
     * ***/
    public static function getCurrentUid(){
        return self::getCurrentTokenVar('uid');
    }
    
    //验证token是否合法或者是否过期
    //验证器验证只是token验证的一种方式
    //另外一种方式是使用行为拦截token，根本不让非法token
    //进入控制器
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            }
            else{
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    // 用户专有权限
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope){
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    public static function needSuperScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope){
            if ($scope == ScopeEnum::Super) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }
    
    public static function isValidOperate($checkedUID){
        if (!$checkedUID){
            throw new Exception('检查UID时必须传入一个检查UID');
        }
        if ($checkedUID==self::getCurrentUid()){
            return true;
        }
        return false;
    }
    
    public static function verifyToken($token){
        $exist=Cache::get($token);
        if ($exist){
            return true;
        }
        return false;
    }
}

