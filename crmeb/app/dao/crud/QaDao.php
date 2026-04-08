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

namespace app\dao\crud;


use app\dao\BaseDao;
use app\model\crud\Qa;

/**
 * Class QaDao
 * @date 2025/05/17
 * @package app\dao\crud
 */
class QaDao extends BaseDao
{

    /**
     * 设置模型
     * @return string
     * @date 2025/05/17
     */
    protected function setModel(): string
    {
        return Qa::class;
    }
    /**
     * 搜索
     * @param array $where
     * @return \crmeb\basic\BaseModel
     * @throws \ReflectionException
     * @date {%DATE%}
     */
    public function searchCrudModel(array $where = [], $field = ['*'], string $order = '', array $with = [])
    {
        return $this->getModel()->field($field)->when($order !== '', function ($query) use ($order) {
            $query->order($order);
        })->when($with, function ($query) use ($with) {
            $query->with($with);
        })->when(!empty($where['nickname']), function($query) use ($where) {
            $query->whereLike('nickname', '%'.$where['nickname'].'%');
        })->when(!empty($where['phone']), function($query) use ($where) {
            $query->whereLike('phone', '%'.$where['phone'].'%');
        })->when(!empty($where['content']), function($query) use ($where) {
            $query->whereLike('content', '%'.$where['content'].'%');
        })->when(!empty($where['create_time']), function($query) use ($where) {
            $query->whereBetween('create_time', $where['create_time']);
        });
    }

}
