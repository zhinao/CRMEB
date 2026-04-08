<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
namespace app\api\controller\v1\qa;

use app\Request;
use app\services\product\product\StoreProductServices;
use app\services\crud\QaServices;
use app\services\system\config\SystemConfigServices;
use app\services\user\UserServices;
/**
 * 商品类
 * Class StoreProductController
 * @package app\api\controller\store
 */
class QaController
{
    /**
     * 商品services
     * @var StoreProductServices
     */
    protected $services;

    public function __construct(QaServices $services)
    {
        $this->services = $services;
        
    }

    //推荐商品
    public function recommendProducts(Request $request)
    {


        $scServices = app()->make(SystemConfigServices::class);
        //$wenjuan =json_decode($scServices->getConfigValue('qa'));


        $post = $request->post();

        // "product_id": [
        //     90,
        //     89,
        //     92
        // ],
        // "product_sel_txt": "油性/中性洗头膏，配合护发素更佳",
        // "advice": [
        //     "及时清洁头皮及头发",
        //     "洗头时水温不宜超过40度",
        //     "护发素应涂抹于发中到发尾",
        //     "减少梳发频率，切忌在湿发时用力梳发或拉扯头发"
        // ]


       $arr_product_id=$post['product_id'];






       $product_sel_txt=$post['product_sel_txt'];
       $advice=$post['advice'];
       $selectId=$post['selectId'];

       $array=explode('-', $selectId);
       $obj = new \stdClass(); // 创建一个空对象
        // 遍历数组，奇数索引为属性名，偶数索引为值
        for ($i = 1; $i < count($array); $i += 2) {
            $property = $array[$i];      // 奇数索引（0, 2, 4...）作为属性名
            $value = $array[$i + 1] ?? null; // 偶数索引（1, 3, 5...）作为值
            $obj->{$property} = $value;  // 动态赋值
        }
        $answers=$obj;

        //return app('json')->success([$product_id,$answers,$product_sel_txt,$advice]);
        
        // $answers=$post['answers'];
        // $selectIndexs=$post['selectIndexs'];

        // $product_id=0;
        // foreach ($selectIndexs as $key => $value) {
        //     $index=$value['index'];
        //     $index2=$value['index2'];
        //     $index3=$value['index3'];
        //     $obj=$wenjuan[$index][$index2][$index3];
        //     if(isset($obj['product_id']))
        //     {
        //         $product_id=$obj['product_id'];
        //         $advice=isset($obj['advice'])?$obj['advice']:'';
        //         break;
        //     }
        // }




        
        // {
        //     "头发长度": "中长发",
        //     "头发物理性质": "细软发",
        //     "头发类型": "每天或隔天洗",
        //     "头皮类型": "头皮油",
        //     "头皮屑情况": "细软发",
        //     "头发易打结/断裂": "是",
        //     "洗发频率": "每天或隔天洗"
        //   }

//const productIds=[92,91,90,89,88];//护发素,短发重油洗发膏,油性洗发膏,中性洗发膏,干性洗发膏
        // switch($jsonData['头发类型']) {
        //     case '容易出油，触摸有油腻感':
        //         $id=$jsonData['头发类型']=='中长发' ? 90 : 91;
        //         break;
        //     case '发丝干枯、毛躁、分叉、打结':
        //         $id=88;
        //         break;
        //     case '不干不油，柔软顺滑有光泽':
        //         $id=89;
        //         break;
        //     default:
        //         $id=92;
        //         break;
        // }



        $type = 0;//活动类型 0=商品，1=秒杀，2=砍价，3=拼团
        $services = app()->make(StoreProductServices::class);

        $products=array();
        //循环 $arr_product_id 数组，获取第一个产品ID
        for ($i=0; $i < count($arr_product_id); $i++) { 
            $product_id = $arr_product_id[$i];
            $data = $services->productDetail($request, (int)$product_id, (int)$type);
            $data['storeInfo']['advice'] =$advice;// '及时清洁，水温≤40℃';
            $product = [
                'id' => $data['storeInfo']['id'],
                'name' => $data['storeInfo']['store_name'],
                'image' => $data['storeInfo']['image'],
                'price' => $data['storeInfo']['price'],
                'advice' => $advice,// $data['storeInfo']['advice'],
                'product_sel_txt' => $product_sel_txt,
            ];
            $products[] = $product;
        }
        
        

        $uid= $request->uid();
        $userServices = app()->make(UserServices::class);
        $userInfo=$userServices->getUserInfo($uid,['nickname','phone']);
        $nickname = $userInfo['nickname'] ?? '';
        $phone = $userInfo['phone'] ?? '';
        $arr = [
            'uid' => $uid,
            'nickname' => $nickname,
            'phone' => $phone,
            'content' => json_encode($answers, JSON_UNESCAPED_UNICODE),
            'product' => json_encode($product, JSON_UNESCAPED_UNICODE),
        ];
        $this->services->crudSave($arr);

        return app('json')->success($products);
    }

    public function wenjuan(Request $request)
    {
        //$scServices = app()->make(SystemConfigServices::class);
        // $wenjuan =json_decode($scServices->getConfigValue('qa'));
        $file = $_SERVER['DOCUMENT_ROOT'] . '/wenda2.json';
        $wenjuan = json_decode(file_get_contents($file), true);

        // if ($request->uid()) {
        //     $user = $request->user();
        // } else {
        //     $user = ['uid' => 0, 'is_promoter' => 0];
        // }

        // $wenjuan = [
        //     [
        //         'title' => '一、基础信息收集',
        //         'content' => [
        //             [
        //                 'title' => '头发长度',
        //                 'content' => ['中长发', '短发', '寸头', '光头']
        //             ],
        //             [
        //                 'title' => '头发物理性质',
        //                 'content' => ['细软发', '粗硬发', '不粗不细']
        //             ],
        //             [
        //                 'title' => '头发类型',
        //                 'content' => ['容易出油，触摸有油腻感', '发丝干枯、毛躁、分叉、打结', '不干不油，柔软顺滑有光泽']
        //             ]
        //         ]
        //     ],
        //     [
        //         'title' => '二、头皮状况诊断',
        //         'content' => [
        //             [
        //                 'title' => '头皮类型',
        //                 'content' => ['头皮油', '头皮干', '头皮不干不油', '不清楚自身头皮类型']
        //             ],
        //             [
        //                 'title' => '头皮屑情况',
        //                 'content' => ['细软发', '粗硬发', '不粗不细']
        //             ],
        //             [
        //                 'title' => '头发类型',
        //                 'content' => ['头皮屑多（易脱落如雪花 / 粘附头皮难脱落）', '头皮屑少']
        //             ]
        //         ]
        //     ],
        //     [
        //         'title' => '三、护发问题筛查',
        //         'content' => [
        //             [
        //                 'title' => '头发易打结/断裂',
        //                 'content' => ['是', '否']
        //             ],
        //             [
        //                 'title' => '洗发频率',
        //                 'content' => ['每天或隔天洗', '每周2-3次', '每周1次或更少']
        //             ]
        //         ]
        //     ]
        // ];





        

        return app('json')->success(['wenjuan' => $wenjuan]);
    }
}
