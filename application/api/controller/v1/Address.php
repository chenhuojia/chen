<?php
namespace app\api\controller\v1;

use app\api\validate\Address as AddressValidata;
use app\api\model\UserAddress;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\common\exception\UserException;
use app\common\exception\SuccessMessage;
use app\common\controller\BaseController;
class Address extends BaseController
{
    
    /**
     * 用户地址新增或者更新
     * @throws UserException
     * @return \think\response\Json
     * ***/
    public function createOrUpdateAddress(){
        $validata=new AddressValidata();
        $validata->goCheck();
        $parmas=$validata->getDataByRule(request()->param());
        $user_id= TokenService::getCurrentUid();
        if (!$user=UserModel::get($user_id)){
            throw new UserException();
        }
        $userAddress=$user->address;
        if (!$userAddress){
           $user->address()->save($parmas);
        }else{
            $user->address->save($parmas);
        }
        return json(new SuccessMessage(),201);
    }
    
    /**
     * 用户地址新增或者更新
     * @throws UserException
     * @return \think\response\Json
     * ***/
    public function getAddress(){
        $user_id= TokenService::getCurrentUid();
        $address=UserAddress::where(['user_id'=>$user_id])->find();
        if (!$address){
            throw new UserException([
                'msg'=>'用户收货地址不存在',
                'errorCode'=>60001
            ]);
        }
        return $address;
    }
    
}

