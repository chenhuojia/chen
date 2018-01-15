<?php
namespace app\api\controller\v1;

use app\api\validate\IDMustBePostiveInt;
use think\Controller;
use app\api\model\Banner as BannerModel;
use app\common\exception\MissException;
class Banner  extends Controller
{
    
    /**
     * 获取指定位置的banner
     * @url /banner/:id
     * @http GET
     * @id banner显示位置id号
     * @return json
     * **/
    
    public function getBanner($id=1){
       (new IDMustBePostiveInt())->goCheck();
        $data=BannerModel::getBannerId($id);
        if (empty($data)){
            throw new MissException();
        }
        return $data;
    }
    
    
    
 
}