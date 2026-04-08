// +---------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2025 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

import LayoutMain from '@/layout';
import setting from '@/setting'

let routePre = setting.routePre

const meta = {
    auth: true,
}

const pre = 'qa_'

export default {
    path: `${routePre}`,
    name: 'crud_qa',
    header: '',
    meta,
    component: LayoutMain,
    children: [
        {
            path: 'crud/qa',
            name: `${pre}list`,
            meta: {
                auth: ['qa-crud-list-index'],
                title: '问答列表',
            },
            component: () => import('@/pages/crud/qa/index'),
        },
    ],
}
