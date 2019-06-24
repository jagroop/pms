<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix">

        <el-row :gutter="20">
          <el-col :span="4">
            <div class="grid-content">
              <span>Activity Logs</span>
            </div>
          </el-col> 
        </el-row>        
        
      </div>
      <el-table
      :data="logs"
      :default-sort = "{prop: 'created_at', order: 'descending'}"
      :row-class-name="tableRowClassName"
      v-loading="table_loading"
      style="width: 100%">    
       <el-table-column
          prop="subject"
          label="Subject"
          sortable>
        </el-table-column>   
        <el-table-column
          prop="title"
          label="Activity"
          sortable>
        </el-table-column>
        <el-table-column
          prop="created_at"
          label="Logged at"
          sortable>
        </el-table-column>
      </el-table>

    </el-card>
  </div>
</template>

<style>
  .el-table .primary-row {
    background: #f0f9eb;
  }
</style>

<script>
import axios from 'axios'
 export default {
  layout: 'app',
  middleware: 'auth',
  metaInfo () {
    return { title: 'Activity Logs' }
  },
  data() {
    return {
      table_loading: true,
      logs: []
    };
  },
  mounted() {
    var self = this;
    axios.get('/api/v1/logs').then(function (response) {
        self.logs = response.data;
        self.table_loading = false;
    })
    .catch(function (error) {
        console.log(error);
    });
  },
  methods: {
    tableRowClassName({row, rowIndex}) {
    if (row.mine == true) {
      return 'primary-row';
    }
    return '';
  }
  }
 }
</script>
