<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span>Users</span>
        <el-button v-if="user.role == 'admin'" style="float: right; padding: 3px 0" type="text" @click="add_user_dialog_show = true">Add User</el-button>
      </div>
      <el-table
      :data="users"
      :default-sort = "{prop: 'created_at', order: 'descending'}"
      v-loading="table_loading"
      style="width: 100%">
       <el-table-column type="expand">
         <template slot-scope="props">
          <p>Tasks</p>
          <ul>
            <li v-for="(task, index) in props.row.tasks"><strong>{{ task.status }}</strong> : {{ task.count }}</li>
          </ul>
          <p>Issues</p>
          <ul>
            <li v-for="(issue, index) in props.row.issues"><strong>{{ issue.status }}</strong> : {{ issue.count }}</li>
          </ul>
         </template>         
        </el-table-column>
        <el-table-column
          prop="name"
          label="Name"
          sortable>
        </el-table-column>
        <el-table-column
          prop="email"
          label="Email"
          sortable>
          <template slot-scope="scope">
            <a :href="'mailto:' + scope.row.email">{{ scope.row.email }}</a>
          </template>
        </el-table-column>
        <el-table-column
          prop="role_formated"
          label="Role" sortable>
          <template slot-scope="scope">
            <el-tag
              disable-transitions>{{scope.row.role_formated}}</el-tag>
          </template>
        </el-table-column>
        <el-table-column
          prop="created_at"
          label="Created At" sortable>
        </el-table-column>
        <el-table-column
          prop="total_tasks"
          label="Today tasks" sortable>
        </el-table-column>
        <el-table-column
          prop="total_issues"
          label="Today issues" sortable>
        </el-table-column>
        <el-table-column
          v-if="user.role == 'admin'"
          label="Actions">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="editUser(scope.row.id)">Edit</el-button>
            <el-button type="text" size="small" @click="deleteUser(scope.row.id)">Delete</el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        background
        layout="prev, pager, next"
        style="float: right; padding: 12px"
        :total="total_users"
        :page-size="per_page"
        @current-change="loadUsers"
        >
      </el-pagination>

    </el-card>   

    <!-- Add User Dialog starts -->
    <el-dialog
    title="Add new user"
    :visible.sync="add_user_dialog_show"
    width="30%">
      <el-form ref="form" :model="form" label-width="120px">
        <el-form-item label="User role">
          <el-select v-model="form.role" placeholder="Select role">
            <el-option label="Developer" value="developer"></el-option>
            <el-option label="Admin" value="admin"></el-option>
            <el-option label="Maintainer" value="maintainer"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Name">
          <el-input v-model="form.name"></el-input>
          <has-error :form="form" field="name" />
        </el-form-item>
        <el-form-item label="Email">
          <el-input v-model="form.email"></el-input>
          <has-error :form="form" field="email" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="form.busy" @click="saveUser">Create</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- Add User Dialog ends -->

    <!-- Edit User Dialog starts -->
    <el-dialog
    title="Edit user"
    :visible.sync="edit_user_dialog_show"
    width="30%">
      <el-form ref="edit_form" :model="edit_form" label-width="120px">
        <el-form-item label="User role">
          <el-select v-model="edit_form.role" placeholder="Select role">
            <el-option label="Developer" value="developer"></el-option>
            <el-option label="Admin" value="admin"></el-option>
            <el-option label="Maintainer" value="maintainer"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Name">
          <el-input v-model="edit_form.name"></el-input>
          <has-error :form="edit_form" field="name" />
        </el-form-item>
        <el-form-item label="Email">
          <el-input v-model="edit_form.email"></el-input>
          <has-error :form="edit_form" field="email" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="edit_form.busy" @click="updateUser">Update</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- Edit User Dialog ends -->

  </div>
</template>

<script>
  import axios from 'axios'
  import { mapGetters } from 'vuex'
  import Form from 'vform'

  export default {
    layout: 'app',
    middleware: 'auth',
    metaInfo () {
      return { title: 'Users' }
    },
    data() {
      return {
        users: [],
        statuses: {
          in_progress: 'success',
          done: 'primary',
          on_hold: 'danger',
        },
        current_user: {
          id: null,
          index: null,
        },
        total_users: 0,
        per_page: 0,
        table_loading: true,
        add_user_dialog_show: false,
        edit_user_dialog_show: false,
        form: new Form({
          role: 'developer',
          name: '',
          email: ''
        }),
        edit_form: new Form({
          role: 'developer',
          name: '',
          email: ''
        }),
      }
    },
    mounted() {
        this.loadUsers(1);
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
      },
      async saveUser() {
        var self = this;
        const { data } = await this.form.post('/api/v1/users');
        self.users.unshift(data);
        self.total_users++;
        self.form.name = '';
        self.form.email = '';
        self.form.role = 'developer';
        self.add_user_dialog_show = false;
      },
      editUser(id) {
        var self = this;

        self.users.forEach((user) => {
          if(user.id == id) {
            self.current_user.id = user.id;
            self.edit_form.name = user.name;
            self.edit_form.role = user.role;
            self.edit_form.email = user.email;
            self.edit_user_dialog_show = true;
          }
        });
      },
      async updateUser() {
        var self = this;
        const user_id = self.current_user.id;
        const { data } = await this.edit_form.patch('/api/v1/users/'+user_id);
        
        self.users.forEach((user) => {
          if(user.id == user_id) {
            user.name = data.name;
            user.email = data.email;
            user.role_formated = data.role_formated;
            user.role = data.role;
            self.edit_user_dialog_show = false;
          }
        });
      },
      deleteUser(id) {
        var self = this;
        self.users.forEach((user, index) => {
          if(user.id === id) {
            this.$confirm('This will permanently delete the record. Continue?', 'Warning', {
              confirmButtonText: 'OK',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              const { data } = this.edit_form.delete('/api/v1/users/'+id);
              self.total_users--;
              self.users.splice(index, 1);
            });  
          }
        });
      }
    },
  }
</script>
