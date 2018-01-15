<?php
namespace app\api\validate;

class Address extends BaseValidate
{
    protected $rule=[
        'name'=>'require|isNotEmpty',
        'mobile'=>'require|isMobile',
        'province'=>'require|isNotEmpty',
        'city'=>'require|isNotEmpty',
        'county'=>'require|isNotEmpty',
        'detail'=>'require|isNotEmpty',
    ];
    protected $message=[
        'name'=>'收件人不能为空',
        'mobile'=>'收件人手机号格式不对',
        'province'=>'省份不能为空',
        'city'=>'城市不能为空',
        'county'=>'国家不能为空',
        'detail'=>'详细地址不能为空',
    ];
}

