<?php
namespace app\api\controller\v1;
use app\api\model\Category as CategoryModel;
use app\common\exception\MissException;
use think\Controller;
class Category extends Controller
{

    /**
     * 获取所有分类
     * @throws MissException
     * @return \think\static[]|\think\false
     * ***/
    public function getCategorys(){
       $categorys=CategoryModel::all([],'img');
       if ($categorys->isEmpty()){
           throw new MissException([
               'msg'=>'数据为空'
           ]);
       }
       return $categorys;
    }
}

