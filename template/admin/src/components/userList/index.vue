<template>
  <div class="goodList">
    <el-form ref="formValidate" :model="formValidate" label-width="80px" label-position="right" inline class="tabform">
      <el-form-item label="用户分组：">
        <el-select v-model="userGroup" clearable @change="userSearchs" class="form_content_width">
          <el-option v-for="item in userGroup" :value="item.id" :key="item.id" :label="item.group_name">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="用户等级：">
        <el-select v-model="userLevel" clearable @change="userSearchs" class="form_content_width">
          <el-option v-for="item in userLevel" :value="item.id" :key="item.id" :label="item.name">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="用户搜索：">
        <el-input
          clearable
          placeholder="请输入用户名称/电话"
          v-model="formValidate.keyword"
          class="form_content_width"
        />
        <el-button type="primary" v-db-click @click="userSearchs" class="ml15">查询</el-button>
      </el-form-item>
    </el-form>

    <el-table
      ref="table"
      empty-text="暂无数据"
      max-height="400"
      :highlight-current-row="many !== 'many'"
      :data="tableList"
      v-loading="loading"
      @select="changeCheckbox"
      @select-all="changeCheckbox"
    >
      <el-table-column v-if="many == 'many'" type="selection" width="55"> </el-table-column>
      <el-table-column v-else width="50">
        <template slot-scope="scope">
          <el-radio v-model="templateRadio" :label="scope.row.id" @change.native="getTemplateRow(scope.row)"
            >&nbsp;</el-radio
          >
        </template>
      </el-table-column>

      <el-table-column label="用户ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.uid }}</span>
        </template>
      </el-table-column>
      <el-table-column label="头像" width="80">
        <template slot-scope="scope">
          <div class="tabBox_img" v-viewer>
            <img v-lazy="scope.row.avatar" />
          </div>
        </template>
      </el-table-column>
      <el-table-column label="用户名称" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.real_name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="电话" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.phone }}</span>
        </template>
      </el-table-column>
      <el-table-column label="设备数量" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.device_num }}</span>
        </template>
      </el-table-column>
      <el-table-column label="分类" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.group_id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="用户等级" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.level }}</span>
        </template>
      </el-table-column>
    </el-table>
    <div class="acea-row row-right page">
      <pagination
        v-if="total"
        :total="total"
        :page.sync="formValidate.page"
        :limit.sync="formValidate.limit"
        @pagination="pageChange"
      />
      <el-button type="primary" v-db-click @click="ok" v-if="many === 'many' && !diy" class="ml15">提交</el-button>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { cascaderListApi, changeListApi } from '@/api/product';
import { userList,levelListApi,userGroupApi } from '@/api/user';
export default {
  name: 'index',
  props: {
    // is_new: {
    //   type: String,
    //   default: '',
    // },
    // type: {
    //   type: Number,
    //   default: 0,
    // },
    // diy: {
    //   type: Boolean,
    //   default: false,
    // },
    // datas: {
    //   type: Object,
    //   default: function () {
    //     return {};
    //   },
    // },
    // selectIds: {
    //   type: Array,
    //   default: () => {
    //     return [];
    //   },
    // },
   
  },
  data() {
    return {
      templateRadio: 0,
      modal_loading: false,
      treeSelect: [],
      formValidate: {
        page: 1,
        limit: 15,
        keyword: '',
      },
      total: 0,
      modals: false,
      loading: false,
      grid: {
        xl: 10,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableList: [],
      userLevel:[],
      userGroup: [],
    };
  },
  computed: {},
  watch: {
    // ischeckbox: {
    //   handler(newVal, oldVal) {
    //     if (newVal) {
    //       this.many = 'many';
    //     }
    //   },
    //   immediate: true,
    // },
  },
  created() {
  },
  mounted() {
    this.userGroup();
    this.userLevel();
    this.getList();

  },
  methods: {
    
    getTemplateRow(row) {
      // let images = [];
      // let imageObject = {
      //   image: row.image,
      //   product_id: row.id,
      //   store_name: row.store_name,
      //   temp_id: row.temp_id,
      // };
      // images.push(imageObject);
      // this.images = images;
      // this.diyVal = row;
      // this.$emit('getProductId', row);
    },
    changeCheckbox(selection) {
      let users = [];
      selection.forEach(function (item) {
        let userObject = {
          uid: item.uid,
          real_name: item.real_name,
          phone: item.phone,
        };
        users.push(userObject);
      });
      this.$emit('getSelectUserInfo', users);
    },
    // 用户分类；
    userGroup() {
      userGroupApi().then((res) => {
        if (res.code === 200) {
          this.userGroup = res.data;
        }
      });
    },
    // 用户等级；
    userLevel() {
      levelListApi().then((res) => {
        if (res.code === 200) {
          this.userLevel = res.data;
        }
      });
    },
    pageChange() {
      this.getList();
    },
    // 列表
    getList() {
      this.loading = true;
      userList(this.formValidate).then((res) => {
        this.loading = false;
        if (res.code === 200) {
          this.tableList = res.data.list;
          this.total = res.data.total;
        }
      });
    },
    ok() {
      if (this.images.length > 0) {
        if (this.$route.query.fodder === 'image') {
          let imageValue = form_create_helper.get('image');
          form_create_helper.set('image', imageValue.concat(this.images));
          form_create_helper.close('image');
        } else {
          this.$refs.table.clearSelection();
          if (this.isdiy) {
            this.$emit('getProductId', this.diyVal);
          } else {
            this.$emit('getProductId', this.images);
          }
        }
      } else {
        this.$message.warning('请先选择商品');
      }
    },
    // 表格搜索
    userSearchs() {
      this.getList();
    },
  },
};
</script>

<style scoped lang="stylus">
.footer {
  margin: 15px 0;
}

.tabBox_img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}

.tabform {
  ::v-deep .ivu-form-item {
    margin-bottom: 16px !important;
  }
}

.btn {
  margin-top: 20px;
  float: right;
}

.goodList {
  ::v-deeptable {
    width: 100% !important;
  }
}
</style>
