// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2025 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import request from '@/libs/request';

/**
 * 获取列表数据
 * @param params
 * @return {*}
 */
export function getQaListApi(params) {
    return request({
        url: 'crud/qa',
        method: 'get',
        params,
    });
}

/**
 * 获取添加表单数据
 * @return {*}
 */
export function getQaCreateApi() {
    return request({
        url: 'crud/qa/create',
        method: 'get',
    });
}

/**
 * 添加数据
 * @param data
 * @return {*}
 */
export function qaSaveApi(data) {
    return request({
        url: 'crud/qa',
        method: 'post',
        data
    });
}

/**
 * 获取编辑表单数据
 * @param id
 * @return {*}
 */
export function getQaEditApi(id) {
    return request({
        url: `crud/qa/${id}/edit`,
        method: 'get'
    });
}

/**
 * 修改数据
 * @param id
 * @return {*}
 */
export function qaUpdateApi(id, data) {
    return request({
        url: `crud/qa/${id}`,
        method: 'put',
        data
    });
}

/**
 * 修改状态
 * @param id
 * @return {*}
 */
export function qaStatusApi(id, data) {
    return request({
        url: `crud/qa/status/${id}`,
        method: 'put',
        data
    });
}

/**
 * 删除数据
 * @param id
 * @return {*}
 */
export function qaDeleteApi(id) {
    return request({
        url: `crud/qa/${id}`,
        method: 'delete'
    });
}

/**
 * 获取数据
 * @param id
 * @return {*}
 */
export function getQaReadApi(id) {
    return request({
        url: `crud/qa/${id}`,
        method: 'get'
    });
}


