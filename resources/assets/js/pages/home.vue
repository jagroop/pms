<template>
  <el-row :gutter="12">
    <el-button @click="loadUsers(1)" type="primary" icon="el-icon-refresh"></el-button>
    <div v-for="(user, index) in users" v-if="user.active && user.all_tasks.length">
      <el-col :span="6" style="padding-bottom: 10px;">
        <el-card class="box-card" style="height: 406px; overflow-y: scroll;">
          <div slot="header" class="clearfix">
            <span>{{ user.name }}</span>
          </div>
          <div v-for="(task ,index, key) in user.all_tasks" :key="key" class="text item" style="padding-bottom: 20px;">
            <span :title="task.project_name+ ' | ' + task.task_name">{{ task.task_name_short }}
              <el-badge :value="task.work_hours" class="item">
                <el-tag :type="statuses[task.task_status]" size="mini" disable-transitions>{{task.task_status_formated}}</el-tag>
              </el-badge>
            </span>
          </div>
        </el-card>
      </el-col>
    </div>
  </el-row>
</template>

<style scoped>
.el-badge.item {
    float: right;
}
</style>
<script>
  import axios from 'axios'
  import { mapGetters } from 'vuex'
  import Form from 'vform'

  export default {
    layout: 'app',
    middleware: 'auth',
    metaInfo () {
      return { title: 'Dashboard' }
    },
    data() {
      return {
        users: [],
        statuses: {
          in_progress: 'primary',
          done: 'success',
          on_hold: 'danger',
          closed: 'danger',
          deployed: 'warning',
          feedback: 'info',
          bug: 'danger',
        },
        current_user: {
          id: null,
          index: null,
        },
        total_users: 0,
        per_page: 0,
        table_loading: true,
        add_user_dialog_show: false,
        edit_user_dialog_show: false
      }
    },
    mounted() {
        var vm = this;
        this.loadUsers(1);
        // setInterval(function(){ 
        //   vm.loadUsers(1);
        // }, 5000);
    },
    computed: mapGetters({
      user: 'auth/user'
    }),
    methods: {
      loadUsers(page) {
       var self = this;
       self.table_loading = true;
       axios.get('/api/v1/users?page='+page)
          .then(function (response) {
              self.total_users = parseInt(response.data.meta.total);
              self.per_page = parseInt(response.data.meta.per_page);
              self.users = response.data.data;
              self.table_loading = false;
          })
          .catch(function (error) {
              console.log(error);
          });
      }
    },
  }
</script>