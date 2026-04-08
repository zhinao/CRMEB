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
 * @date 2025/05/17
 */

namespace app\adminapi\controller\crud;

use app\adminapi\controller\AuthController;
use think\facade\App;
use app\services\crud\QaServices;

/**
 * Class Qa
 * @date 2025/05/17
 * @package app\adminapi\controller\crud\crmeb\
 */
class Qa extends AuthController
{

    /**
     * @var QaServices
     */
    protected $service;

    /**
     * QaController constructor.
     * @param App $app
     * @param QaServices $service
     */
    public function __construct(App $app, QaServices $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * 列表
     * @date 2025/05/17
     * @return \think\Response
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['nickname', ''],
            ['phone', ''],
            ['content', ''],
            ['create_time', ''],
        ]);
        return app('json')->success($this->service->getCrudListIndex($where));
    }

    /**
     * 创建
     * @return \think\Response
     * @date 2025/05/17
     */
    public function create()
    {
        return app('json')->success($this->service->getCrudForm());
    }

    /**
     * 保存
     * @return \think\Response
     * @date 2025/05/17
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['uid', ''],
            ['nickname', ''],
            ['phone', ''],
            ['content', ''],
            ['create_time', ''],

        ]);

        validate(\app\adminapi\validate\crud\QaValidate::class)->check($data);

        $this->service->crudSave($data);

        return app('json')->success(100021);
    }

    /**
     * 编辑获取数据
     * @param $id
     * @return \think\Response
     * @date 2025/05/17
     */
    public function edit($id)
    {
        return app('json')->success($this->service->getCrudForm((int)$id));
    }

    /**
     * 修改
     * @param $id
     * @return \think\Response
     * @date 2025/05/17
     */
    public function update($id)
    {
        if (!$id) {
            return app('json')->fail(100100);
        }

        $data = $this->request->postMore([
            ['uid', ''],
            ['nickname', ''],
            ['phone', ''],
            ['content', ''],
            ['create_time', ''],

        ]);

        validate(\app\adminapi\validate\crud\QaValidate::class)->check($data);

        $this->service->crudUpdate((int)$id, $data);

        return app('json')->success(100001);
    }

    /**
     * 修改状态
     * @param $id
     * @return \think\Response
     * @date 2025/05/17
     */
    public function status($id)
    {
        if (!$id) {
            return app('json')->fail(100100);
        }

        $data = $this->request->postMore([
            ['field', ''],
            ['value', '']
        ]);

        $filedAll = [];

        if (!in_array($data['field'], $filedAll)) {
            return app('json')->fail(100100);
        }

        if ($this->service->update(['id'=> $id], [$data['field']=> $data['value']])) {
            return app('json')->success(100001);
        } else {
             return app('json')->fail(100100);
        }
    }

    /**
     * 删除
     * @param $id
     * @return \think\Response
     * @date 2025/05/17
     */
    public function delete($id)
    {
        if (!$id) {
            return app('json')->fail(100100);
        }

        if ($this->service->destroy((int)$id)) {
            return app('json')->success(100002);
        } else {
            return app('json')->success(100008);
        }
    }

    /**
     * 查看
     * @param $id
     * @return \think\Response
     * @date 2025/05/17
     */
    public function read($id)
    {
        if (!$id) {
            return app('json')->fail(100100);
        }

        $info = $this->service->get($id, ['*'], []);
        if (!$info) {
            return app('json')->fail(100100);
        }

        return app('json')->success($info->toArray());
    }



}
