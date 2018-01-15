<?php
namespace app\api\controller\v1;

use think\Controller;
use app\api\validate\OrderPlace;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
use app\api\validate\PagingParameter;
use app\api\model\Order as OrderModel;
use app\api\validate\IDMustBePostiveInt;
use app\common\exception\OrderException;
class Order extends Controller
{
    /**
     * 统一下单接口
     * @params [['product_id'=>1,'num'=>1]] 
     * @return json 
     ***/
    public function placeOrder(){
        (new OrderPlace())->gocheck();
        $products=input('post.products/a');
        $uid=TokenService::getCurrentUid();
        //$uid=1;
        $order=new OrderService();
        $status=$order->place($uid, $products);
        return $status;
    }
    
    
    public function getSummaryByUser($page=1,$size=15){
        (new PagingParameter())->goCheck();
        $uid=TokenService::getCurrentUid();
        $order=OrderModel::getSummaryByUser($uid,$page,$size);
        return $order;
    }
    
    
    
    public function getDetial($id){
        (new IDMustBePostiveInt())->goCheck();
        $order=OrderModel::get($id);
        if (!$order){
            throw new OrderException();
        }
        return $order->hidden(['prepay_id']);
    }
}

