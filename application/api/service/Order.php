<?php
namespace app\api\service;

use app\api\model\Product as ProductModel;
use app\common\exception\OrderException;
use app\api\model\UserAddress;
use app\common\exception\UserException;
use think\Exception;
use think\Db;
use app\common\enum\OrderStatusEnum;
use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct;

class Order
{
    protected $oProducts;
    
    protected $products;
    
    protected $uid;
    
    
    /**
     * 下单
     * @param unknown $uid
     * @param unknown $oproducts
     * @return number|\app\api\service\string[]|\app\api\service\unknown[]
     * ***/
    public  function place($uid,$oproducts){
        $this->oProducts=$oproducts;
        $this->uid=$uid;
        $this->products=$this->getProductByOrder($oproducts);
        $status=$this->getOrderStatus();
        if ($status['pass']==false){
            $status['order_id']=-1;
            return $status;
        }
        $snap=$this->snapOrder($status);
        $order=$this->createOrder($snap);
        $order['pass']=true;
        return $order;
    }

    /**
     * 通过订单id库存量检查
     * @param unknown $orderID
     * @return array
     * ***/
    public function checkOrderStock($orderID){
       $this->oProducts=OrderProduct::where(['order_id'=>['=',$orderID]])->select();
       $this->products=$this->getProductByOrder($this->oProducts);
       $orderStatus=$this->getOrderStatus();
       return $orderStatus;
    }
    
    /**
     * 创建订单写入数据库
     * @param unknown $snap
     * @throws Exception
     * @return string[]|unknown[]
     * ***/
    private function createOrder($snap){
        Db::startTrans();
        try {
            $orderNo=$this->makeOrderNo();
            $order=new OrderModel();
            $order->user_id=$this->uid;
            $order->order_no=$orderNo;
            $order->total_price=$snap['orderPrice'];
            $order->snap_img=$snap['snapImg'];
            $order->snap_name=$snap['snapName'];
            $order->total_count=$snap['totalCount'];
            $order->snap_address=$snap['snapAddress'];
            $order->snap_items=json_encode($snap['pStatus']);
            $order->save();
            $orderID=$order->id;
            $create_time=$order->create_time;
            foreach ($this->oProducts as &$p){
                $p['order_id']=$orderID;
            }
            $orderProduct=new \app\api\model\OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            throw $e;
        }
        return [
            'order_no'=>$orderNo,
            'order_id'=>$orderID,
            'create_time'=>$create_time,
        ];
    }
    
    /**
     * 获取用户收件地址
     * @throws UserException
     * ***/
    private function getUserAddress(){
        $userAddress=UserAddress::where('user_id','=',$this->uid)->field('id,name,mobile,mobile,province,city,county,detail,user_id')->find();
        if (empty($userAddress)){
            throw new UserException([
                'msg'=>'用户收货地址不存在，下单失败',
            ]);
        }
        return $userAddress->toArray();
    }
    
    /**
     * 生成订单快照
     * @param unknown $status
     * @return boolean[]|string[]|unknown[]|NULL[]|\app\api\service\unknown[]
     * ***/
    private function snapOrder($status){
        $snap=[
            'orderPrice'=>$status['orderPrice'],
            'totalCount'=>$status['totalCount'],
            'pStatus'=>[],
            'snapAddress'=>'',
            'snapName'=>'',
            'snapImg'=>'',
        ];
        $snap['snapAddress']=json_encode($this->getUserAddress());
        $snap['snapName']=$this->products[0]['name'];
        $snap['snapImg']=$this->products[0]['main_img_url'];
        if (count($this->products)>1){
            $snap['snapName']=$snap['snapName'].'等';
        } 
        for ($i = 0; $i < count($this->products); $i++) {
            $product = $this->products[$i];
            $oProduct = $this->oProducts[$i];
        
            $pStatus = $this->snapProduct($product, $oProduct['count']);
            $snap['orderPrice'] += $pStatus['totalPrice'];
            $snap['totalCount'] += $pStatus['count'];
            array_push($snap['pStatus'], $pStatus);
        }
        return $snap;
    }
    
    // 单个商品库存检测
    private function snapProduct($product, $oCount)
    {
        $pStatus = [
            'id' => null,
            'name' => null,
            'main_img_url'=>null,
            'count' => $oCount,
            'totalPrice' => 0,
            'price' => 0
        ];
    
        $pStatus['counts'] = $oCount;
        // 以服务器价格为准，生成订单
        $pStatus['totalPrice'] = bcmul($oCount,$product['price'],2);
        $pStatus['name'] = $product['name'];
        $pStatus['id'] = $product['id'];
        $pStatus['main_img_url'] =$product['main_img_url'];
        $pStatus['price'] = $product['price'];
        return $pStatus;
    }
    
    /**
     * 统计及检测当前订单快照
     * @return boolean[]|number[]|string[]
     * ***/
    private function getOrderStatus(){
        $status=[
            'pass'=>true,
            'orderPrice'=>0,
            'totalCount'=>0,
            'pStatusArray'=>[],
        ];       
        foreach ($this->oProducts as $v){
            $pStatus=$this->getProductStatus($v['product_id'], $v['count'], $this->products);
            if ($pStatus['haveStock']==false){
                $status['pass']=false;
            }
            $status['orderPrice'] = bcadd($status['orderPrice'], $pStatus['totalPrice'],2);
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'], $pStatus);
        }
        return $status;
    }
    
    /**
     * 检测商品库存
     * @param unknown $product_id
     * @param unknown $product_count
     * @param unknown $products
     * @throws OrderException
     * @return NULL[]|boolean[]|number[]|unknown[]|string[]
     * ***/
    private function getProductStatus($product_id,$product_count,$products){
        $pIndex=-1;
        $pStatus=[
            'id'=>null,
            'haveStock'=>false,
            'count'=>0,
            'name'=>null,
            'price'=>0,
            'main_img_url'=>null,
            'totalPrice'=>0,
        ];
        for($i=0;$i<count($products);$i++){
            if ($product_id == $products[$i]['id']){
                $pIndex=$i;
            }
        }
        if ( $pIndex == -1){
            throw new OrderException([
                'msg'=>'id为'.$product_id.'的商品不存在,创建订单失败',
            ]);
        }else{
            $product = $products[$pIndex];
            $pStatus['id']=$product['id'];
            $pStatus['count']=$product_count;
            $pStatus['name']=$product['name'];
            $pStatus['price']=$product['price'];
            $pStatus['main_img_url']=$product['main_img_url'];
            $pStatus['totalPrice']=bcmul($product_count,$product['price'],2);
            if (($product['stock']-$product_count) >= 0){
                $pStatus['haveStock']=true;
            }
        }
        return $pStatus;
    }
    
    /**
     * 获取数据库中的product
     * @param unknown $oproducts
     * @return unknown
     * ***/
    public static function getProductByOrder($oproducts){
        $productID=[];
        foreach ($oproducts as $oproduct){
            array_push($productID, $oproduct['product_id']);
        }
        $products=ProductModel::all($productID)->visible(['id', 'price', 'stock', 'name', 'main_img_url'])->toArray();
        return $products;
    }
    
    /**
     * 创建订单号
     * @return string
     * ***/
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
        $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
            'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }
    
    
    public static function updateOrderStatus($orderID,$success){
        $status=$success?OrderStatusEnum::PAID:OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id','=',$orderID)->update(['status'=>$status]);
    }
    
    public static function reduceStock($stockStatus){
    
        foreach ($stockStatus['pStatusArray'] as $signlePStatus){
            Product::where('id','=',$signlePStatus['product_id'])->setDec('stock',$signlePStatus['count']);
        }            
    }
    
}

