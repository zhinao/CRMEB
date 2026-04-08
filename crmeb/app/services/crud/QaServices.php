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

namespace app\services\crud;

use app\services\BaseServices;
use think\exception\ValidateException;
use app\dao\crud\QaDao;
use crmeb\services\FormBuilder;

/**
 * Class CrudService
 * @date 2025/05/17
 * @package app\services\crud\crmeb\
 */
class QaServices extends BaseServices
{

    /**
     * QaServices constructor.
     * @param QaDao $dao
     */
    public function __construct(QaDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 主页数据接口
     * @param array $where
     * @return array
     * @date 2025/05/17
     */
    public function getCrudListIndex(array $where = [])
    {
        [$page, $limit] = $this->getPageValue();
        $model = $this->dao->searchCrudModel($where, 'nickname,phone,content,create_time,id', 'id desc', []);

        return ['count' => $model->count(), 'list' => $model->page($page ?: 1, $limit ?: 10)->select()->toArray()];
    }

    /**
     * 编辑和获取表单
     * @date 2025/05/17
     * @param int $id
     * @return array
     */
    public function getCrudForm(int $id = 0)
    {
        $url = '/crud/qa';
        $info = [];
        if ($id) {
            $info = $this->dao->get($id);
            if (!$info) {
                throw new ValidateException(100026);
            }
            $url .= '/' . $id;
        }
        $rule = [];

        $rule[] = FormBuilder::input("uid", "uid",  $info["uid"] ?? '');
        $rule[] = FormBuilder::input("nickname", "昵称",  $info["nickname"] ?? '');
        $rule[] = FormBuilder::input("phone", "手机号码",  $info["phone"] ?? '');
        $rule[] = FormBuilder::input("content", "内容",  $info["content"] ?? '');
        $rule[] = FormBuilder::dateTime("create_time", "添加时间",  $info["create_time"] ?? '');

        return create_form('', $rule, $url, $id ? 'PUT' : 'POST');
    }

    /**
     * 新增
     * @date 2025/05/17
     * @param array $data
     * @return mixed
     */
    public function crudSave(array $data)
    {
        return $this->dao->save($data);
    }

    /**
     * 修改
     * @date 2025/05/17
     * @param int $id
     * @param array $data
     * @return \crmeb\basic\BaseModel
     */
    public function crudUpdate(int $id, array $data)
    {
        return $this->dao->update($id, $data);
    }



}
