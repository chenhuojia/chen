<?php
namespace app\api\model;
use think\Model;
class ProductProperty extends Model
{
    protected $hidden=['product_id','update_time','delete_time', 'id'];
    //protected $table='product_property';
}

