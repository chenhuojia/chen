<?php
namespace app\api\service;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Loader;
use think\Exception;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Notify.php');
class AlipayNotify extends \AlipayNotify
{
    
    public function NotifyProcess($data, $msg){
        if ($data['result_code']=='SUCCESS'){
            $orderNo=$data['out_trade_no'];
            try {
                $order=OrderModel::where('order_no','=',$orderNo)->find();
                if ($order->status==1){
                    $service=new OrderService();
                    $status=$service->checkOrderStock($order->id);
                    if ($status['pass']){
                        $service->updateOrderStatus($order->id, true);
                        $service->reduceStock($status);
                    }else{
                        $service->updateOrderStatus($order->id,false);
                    }
                }
                return true;
            }catch (Exception $ex){
                log::record($ex,'error');
                return false;
            }
        }
        return true;
    }
    
    
}

