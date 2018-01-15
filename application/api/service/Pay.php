<?php
namespace app\api\service;

use app\common\exception\ParmaeterException;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\common\exception\OrderException;
use app\common\exception\TokenException;
use app\common\enum\OrderStatusEnum;
use think\Loader;
use think\Log;
use app\api\service\Token;
class Pay
{
    private $orderID;
    private $orderNO;
    private $config=[];
    
    public function __construct($orderID){
        if (empty($orderID)){
            throw new ParmaeterException(['msg'=>'订单ID不能为空']);
        }
        $this->orderID=$orderID;
        $this->config=[
            'wx'=>array_key_exists('wechat',config())?config('wechat'):[],
            'alipay'=>array_key_exists('alipay',config())?config('alipay'):[],
        ];
    }
    
    public function wxPay(){
        Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
        $this->checkOrderValidata();
        $orderService=new OrderService();
        $orderStatus=$orderService->checkOrderStock($this->orderID);
        if (!$orderStatus['pass']){
            return $orderStatus;
        }
        return $this->makeWxPreOrder($orderStatus['orderPrice']);
    }
    
    public function aliPay(){
        $this->checkOrderValidata();
        $orderService=new OrderService();
        $orderStatus=$orderService->checkOrderStock($this->orderID);
        if (!$orderStatus['pass']){
            return $orderStatus;
        }
        return $this->makeAliPayPreOrder($orderStatus['orderPrice']);
    }
    
    
    private function makeAliPayPreOrder($totalPrice){
        Loader::import('Alipay.pagepay.service.AlipayTradeService',EXTEND_PATH);
        Loader::import('Alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder',EXTEND_PATH);
        $out_trade_no = time();
        //订单名称，必填
        $subject = 'testst';
        //付款金额，必填
        $total_amount = $totalPrice;
        //商品描述，可空
        $body = '测试测试';
        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $aop = new \AlipayTradeService($this->config['alipay']);
        $response = $aop->pagePay($payRequestBuilder,$this->config['alipay']['return_url'],$this->config['alipay']['notify_url']);
        //输出表单
        var_dump($response);
    }
    
    
    private function makeWxPreOrder($totalPrice){
        $openid=Token::getCurrentTokenVar('openid');
        if (!$openid){
            throw new TokenException();
        }
        $wxOrder=new \WxPayUnifiedOrder();
        $wxOrder->SetOut_trade_no($this->orderNO);
        $wxOrder->SetTrade_type('JSAPI');
        $wxOrder->SetTotal_fee($totalPrice*100);
        $wxOrder->SetBody('test');
        $wxOrder->SetOpenid($openid);
        $wxOrder->SetNotify_url($this->config['wx']['notify_url']);
        return $this->getPaySignature($wxOrder);
    }
    
    private function sign($wxOrder){
        $jsApiPayData=new \WxPayJsApiPay();
        $jsApiPayData->SetAppid($this->config['wx']['app_id']);
        $jsApiPayData->SetTimeStamp((string)time());
        $rand=md5(time().mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign=$jsApiPayData->MakeSign();
        $rowvalues=$jsApiPayData->GetValues();
        $rowvalues['paySign']=$sign;
        unset($rowvalues['appid']);
        return $rowvalues;
    }
    
    private function getPaySignature($wxOrderData){
        return $wxOrder= \WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('微信预支付订单失败','error');
        }
        $this->recodePrepayid($wxOrder);
        $signature=$this->sign($wxOrder);
        return $signature;
    }
    
    private function recodePrepayid($wxOrder){
        OrderModel::where('id','=',$this->orderID)->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }
    
    /**
     * 订单校验
     * @throws OrderException
     * @throws TokenException
     * @return boolean
     * ***/
    private  function  checkOrderValidata(){
        $order=OrderModel::where(['id'=>['=',$this->orderID]])->find();
        if (empty($order)){
            throw new OrderException();
        }
        if (!Token::isValidOperate($order->user_id)){
            throw new TokenException([
                'msg'=>'订单与用户不匹配',
                'errorCode'=>10003,
            ]);
        }
        if ($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'msg'=>'订单已经支付过了',
                'errorCode'=>80003,
                'code'=>400,
            ]);
        }
        $this->orderNO=$order->order_no;
        return true;
    }
    
    
    
    
}

