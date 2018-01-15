<?php
namespace app\api\controller\v1;
use app\common\controller\BaseController;
use app\api\service\Pay as PayService;
use app\api\service\WxNotify;
class Pay extends BaseController{

    public function getPreOrder($orderId=0){
        
        //return  $order=Order::where(['id'=>['=',1]])->find();
        $pay=new PayService($orderId);
        return $pay->wxPay();
    }

    public function receiveWxNotify(){
        $notify=new WxNotify();
        $notify->Handle();
    }


}