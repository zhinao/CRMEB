<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2025 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------
 */

/**
 * 
 * @author crud自动生成代码
 * @date 2025/05/17 20:57:36
 */

namespace app\adminapi\validate\crud;


use think\Validate;

/**
 * Class CrudValidate
 * @date 2025/05/17
 * @package app\adminapi\validate\crud\crmeb\
 */
class QaValidate extends Validate
{

    /**
     * @var array
     */
    protected $rule = [
        'uid'=> 'require',
    ];

    /**
     * @var array
     */
    protected $message = [
        'uid.require'=> 'uid必须填写',
    ];

    /**
     * @var array
     */
    protected $scene = [

    ];
}
