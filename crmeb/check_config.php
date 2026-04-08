<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------

// 加载框架
require __DIR__ . '/thinkphp/base.php';

// 使用系统配置服务
use crmeb\services\SystemConfigService;

// 获取配置
$wechatConfig = SystemConfigService::more(['wechat_appid', 'wechat_app_appid', 'pay_weixin_open', 'pay_weixin_type']);

// 输出配置信息
echo 'wechat_appid: ' . ($wechatConfig['wechat_appid'] ?? '未配置') . PHP_EOL;
echo 'wechat_app_appid: ' . ($wechatConfig['wechat_app_appid'] ?? '未配置') . PHP_EOL;
echo 'pay_weixin_open: ' . ($wechatConfig['pay_weixin_open'] ?? '未配置') . PHP_EOL;
echo 'pay_weixin_type: ' . ($wechatConfig['pay_weixin_type'] ?? '未配置') . PHP_EOL;