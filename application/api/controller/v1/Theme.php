<?php
namespace app\api\controller\v1;

use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\common\exception\MissException;
use app\api\validate\IDMustBePostiveInt;
class Theme
{

   /**
    * @url /theme?ids=id1,id2
    * @return json $data
    * ***/
   public function getThemeList($ids=''){
       (new IDCollection())->goCheck();
        $ids=explode(',', $ids);
        $result=ThemeModel::with('topicImg,headImg')->field('id,name,description,topic_img_id,head_img_id')->select($ids);
        if ($result->isEmpty()){
            throw new MissException(['msg'=>'找不到资源，请输入正确ID']);
        }
        return $result;
   }
   
   
   public function getThemeOne($id){
       (new IDMustBePostiveInt())->goCheck();
       $result = ThemeModel::themeProducts($id);
       if (empty($result)){
           throw new MissException(['msg'=>'找不到资源，请输入正确ID']);
       }
       return $result;
   }
}

