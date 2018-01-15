<?php
use think\Route;
//Route::miss('api/v1.Index/index');

/**
 * banner 路由
 * ***/
Route::group('api/:version/banner',[
    '/:id'  =>  ['api/:version.Banner/getBanner', ['method' => 'get']],
]);

/**
 * 分类路由
 * ***/
Route::group('api/:version/category',[
    '/all'   => ['api/:version.Category/getCategorys', ['method' => 'get']],
]);

/**
 * 主题路由
 * ***/
Route::group('api/:version/theme',[
    '/list'=>['api/:version.Theme/getThemeList'],
    '/:id'=> ['api/:version.Theme/getThemeOne',[],['id' => '\d+']],
],['method'=>'get']);

/**
 * 商品路由
 * ***/
Route::group('api/:version/product',[
    '/recent/[:skip]/[:count]'=>['api/:version.Product/getNewProducts'],
    '/category/:id'=>['api/:version.Product/getCategoryProducts'],
    '/:id'=>['api/:version.Product/getProductOne',[],['id' => '\d+']]
],['method'=>'get']);

/**
 * token路由
 * ***/
Route::group('api/:version/token',[
    'user'=>['api/:version.Token/getToken'],
    'verify'=>['api/:version.Token/verifyToken'],
],['method'=>'post']);

/**
 * 用户相关信息路由
 * ***/
Route::group('api/:version/user',[
    'address'=>['api/:version.Address/createOrUpdateAddress'],
    'getAddress'=>['api/:version.Address/getAddress'],
],['method'=>'post']);


Route::group('api/:version/order',[
    'place'=>['api/:version.Order/placeOrder'],
    'byUser'=>['api/:version.Order/getSummaryByUser'],
    '/:id'=>['api/:version.Order/getDetial',[],['id' => '\d+']],
    'getPre'=>['api/:version.Pay/getPreOrder'],
    'wxNotify'=>['api/:version.Pay/receiveWxNotify'],
],['method'=>'post']);

