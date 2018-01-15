<?php
namespace app\common\behavior;
use app\api\service\Token as TokenService;

class AuthBehavior
{
    public function run(&$parmas){
        TokenService::needPrimaryScope();
    }
}

