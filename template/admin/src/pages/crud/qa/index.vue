<template>
  <div>
    <el-card shadow="never" class="ivu-mt" :body-style="{padding:0}">
        <div class="padding-add">
           <el-form
             ref="curlFrom"
             :model="from"
             :label-width="labelWidth"
             :label-position="labelPosition"
             inline
             @submit.native.prevent
           >
        <el-form-item label="昵称：" label-for="nickname">
            <el-input
                v-model="from.nickname"
                placeholder="请输入昵称"
                class="form_content_width"
            />
        </el-form-item>

        <el-form-item label="手机号码：" label-for="phone">
            <el-input
                v-model="from.phone"
                placeholder="请输入手机号码"
                class="form_content_width"
            />
        </el-form-item>

        <el-form-item label="内容：" label-for="content">
            <el-input
                v-model="from.content"
                placeholder="请输入内容"
                class="form_content_width"
            />
        </el-form-item>

<el-form-item label="添加时间:">
    <el-date-picker
        :editable="false"
        clearabl
        @change="searchs"
        v-model="from.create_time"
        format="yyyy/MM/dd"
        type="daterange"
        value-format="yyyy/MM/dd"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        style="width:250px;"
    ></el-date-picker>
</el-form-item>

              <el-form-item>
                 <el-button type="primary" @click="searchs">查询</el-button>
              </el-form-item>
           </el-form>
        </div>
    </el-card>

    <el-card shadow="never" dis-hover class="ivu-mt mt16">
      <!-- <el-row type="flex">
        <el-col v-bind="grid">
          <el-button v-auth="['qa-add']" type="primary" icon="md-add" @click="add">添加</el-button>
        </el-col>
      </el-row> -->
      <el-table
          :data="dataList"
          ref="table"
          class="mt25"
          :loading="loading"
          highlight-current-row
      >
        <el-table-column prop="nickname" label="昵称">
        </el-table-column>
        <el-table-column prop="phone" label="手机号码">
        </el-table-column>
        <el-table-column prop="content" label="内容">
        </el-table-column>
        <el-table-column prop="create_time" label="添加时间">
        </el-table-column>
        <el-table-column label="操作">
            <template slot-scope="scope">
              <a @click="show(scope.row)">详情</a>
              <el-divider direction="vertical" />
               <!-- 
              <a @click="edit(scope.row.id)">修改</a>
              <el-divider direction="vertical" /> -->
              <a @click="del(scope.row, '删除', scope.$index)">删除</a>
            </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination :total="total"  @pagination="pageChange"  :limit.sync="from.limit" :page.sync="from.page" />
      </div>
    </el-card>

    <el-dialog title="查看详情" :visible.sync="dialogTableVisible" v-if='dialogTableVisible'>
      <el-descriptions title="">
        <el-descriptions-item label="昵称">{{info.nickname}}</el-descriptions-item>
        <el-descriptions-item label="手机号码">{{info.phone}}</el-descriptions-item>
        <el-descriptions-item label="添加时间">{{info.create_time}}</el-descriptions-item>
      </el-descriptions>

      <el-descriptions title="客户回答：" style="margin-top: 10px;margin-bottom: 0px;color:red;" border column="1">

        <el-descriptions-item :label="index" v-for="(item,index) in info.content" >{{item}}</el-descriptions-item>

      </el-descriptions>

      <el-descriptions title="推荐商品：" style="margin-top: 10px;margin-bottom: 0px;color:#009900;">
        <el-descriptions-item >{{ (info.product instanceof Array? info.product[0].product_sel_txt : info.product.product_sel_txt)}}</el-descriptions-item>
      </el-descriptions>

      




<el-descriptions border column="1" style="margin-top: 0px;margin-bottom: 5px;color:#999;">     
<template v-for="(item, index) in  (info.product instanceof Array ? info.product : [info.product])">

        <el-descriptions-item label="商品名称">
          {{ item.name }}
        </el-descriptions-item>

        <el-descriptions-item label="商品图片">
          <img :src="item.image" alt="商品图片" class="product-image" />
        </el-descriptions-item>
        <el-descriptions-item label="价格">
          ¥{{ item.price }}
        </el-descriptions-item>

        
</template>


        <el-descriptions-item label="护发建议">

          <el-tag v-for="(item, index) in  (info.product instanceof Array? info.product[0].advice : info.product.advice)" :key="index">
            {{ item }}
          </el-tag>

        </el-descriptions-item>
</el-descriptions>

     




    </el-dialog>

  </div>
</template>

<script>
import { mapState } from 'vuex';
import { qaSaveApi, qaStatusApi, qaDeleteApi, qaUpdateApi, getQaCreateApi, getQaEditApi, getQaListApi, getQaReadApi} from '@/api/crud/qa';
export default {
  name: 'qa',
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      from: {
        nickname:'',
        phone:'',
        content:'',
        create_time:'',
        page: 1,
        limit: 15,
      },
      dataList: [],
      total: 0,
      dialogTableVisible: false,
      info: {},
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '75px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'left';
    },
  },
  created() {
    this.getList();
  },
  methods: {
    show(row) {
        getQaReadApi(row.id).then(res => {
           this.dialogTableVisible = true;
           this.info = res.data;
           this.info.content=JSON.parse(res.data.content);  
           this.info.product=JSON.parse(res.data.product);
        }).catch(res => {
          this.$Message.error(res.msg);
        })
    },
    //修改状态
    updateStatus(row, field) {
        qaStatusApi(row.id, {field: field, value: row[field]})
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // 添加
    add() {
      this.$modalForm(getQaCreateApi()).then(() => this.getList());
    },
    // 表格搜索
    searchs() {
      this.from.page = 1;
      this.getList();
    },
    //列表
    getList() {
      this.loading = true;
      getQaListApi(this.from)
          .then(async (res) => {
            let data = res.data;
            this.dataList = data.list;
            this.total = data.count;
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$Message.error(res.msg);
          });
    },
    //分页
    pageChange(index) {
      this.from.page = index;
      this.getList();
    },
    // 修改
    edit(id) {
      this.$modalForm(getQaEditApi(id)).then(() => this.getList());
    },
    // 删除
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `crud/qa/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
          .then((res) => {
            this.$Message.success(res.msg);
            this.getList();
          })
          .catch((res) => {
            this.$Message.error(res.msg);
          });
    },
  },
};
</script>

<style scoped>
.product-image {
  width: 150px;
  height: 150px;
  border-radius: 8px;
  object-fit: cover;
}
</style>
