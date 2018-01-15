<?php
namespace app\common\controller;

use think\Controller;
use think\Hook;
use think\Request;

class BaseController extends Controller
{
    /* public function _initialize(){
        $request=Request::instance();
        echo $request->module().':'.$request->controller().":".$request->action();
        exit;
        //Hook::listen('checkAuth');
    } */
    
}

