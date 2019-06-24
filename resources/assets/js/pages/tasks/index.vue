<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix">

        <el-row :gutter="20">
          <el-col :span="4">
            <div class="grid-content">
              <span>Tasks</span>
            </div>
          </el-col>
          <el-col :span="16">
            <div class="grid-content">
              <div class="filters">
                <el-form :inline="true" label-width="120px">
                  <el-form-item label="Filters : " prop="filters" style="margin:0;">
                      <el-checkbox @change="loadTasks(1)" label="Assinged to me" v-model="filters.user_id" name="user_id"></el-checkbox>
                      <el-checkbox @change="loadTasks(1)" label="Today" v-model="filters.date" name="date"></el-checkbox>
                  </el-form-item>

                  <el-form-item prop="filters" style="margin-left: 18px;">
                      <el-select @change="loadTasks(1)" v-model="filters.project_id" filterable placeholder="Select Project">
                        <el-option label="All" value="all"></el-option>
                        <el-option v-for="project in projects" :label="project.name" :value="project.id" :key="project.id"></el-option>
                      </el-select>                    
                  </el-form-item>

                </el-form>
              </div>
            </div>
          </el-col>
          <el-col :span="4">
            <div class="grid-content">
              <el-button style="float: right; padding: 3px 0" type="text" @click="add_task_dialog_show = true">Add Task</el-button>              
            </div>
          </el-col>
        </el-row>        
        
      </div>
      <el-table
      :data="tasks"
      :default-sort = "{prop: 'created_at', order: 'descending'}"
      v-loading="table_loading"
      style="width: 100%">
        <el-table-column type="expand">
          <template slot-scope="props">
            <p><strong>Description:</strong> {{ props.row.task_desc }}</p>
            <p><strong>Assigned By:</strong> {{ props.row.assigned_by }}</p>
            <p><strong>Billing Hours:</strong> {{ props.row.billing_hours }}</p>
            <p><strong>Work Hours:</strong> {{ props.row.work_hours }}</p>
            <p><strong>Activity Logs:</strong> </p>
            <el-breadcrumb separator-class="el-icon-arrow-right">
              <el-breadcrumb-item v-for="(activity, ind) in props.row.activity" :key="ind" :title="activity.created_at +' | '+activity.created_at_human">{{ activity.label }}</el-breadcrumb-item>
            </el-breadcrumb>
            <br>
            <p><strong>Attachments:</strong> </p>
            <ul>
                <li v-for="(file, index) in props.row.files" :key="index"><a :href="file.full_url" target="_blank">{{ file.file_name }}</a> | {{ file.created_at }}</li>
            </ul>
          </template>
        </el-table-column>
        <el-table-column
          prop="id"
          label="Task ID"
          sortable>
        </el-table-column>
        <el-table-column
          prop="project_name"
          label="Project Name"
          sortable>
        </el-table-column>
        <el-table-column
          prop="task_name"
          label="Task Name"
          sortable>
        </el-table-column>
        <el-table-column
          prop="status_formated"
          label="Status" sortable>
          <template slot-scope="scope">
            <el-tag
              :type="statuses[scope.row.task_status]"
              disable-transitions>{{scope.row.task_status_formated}}</el-tag>
          </template>
        </el-table-column>
        <el-table-column
          prop="completion_precentage"
          label="Progress"
          sortable>
          <template slot-scope="scope">
            <el-progress style="width: 55%" :text-inside="true" :stroke-width="14" :status="scope.row.percentage_status" :percentage="scope.row.completion_precentage"></el-progress>
          </template>
        </el-table-column>
        <el-table-column
          prop="assigned_to"
          label="Assigned To" sortable>
        </el-table-column>
        <el-table-column
          prop="created_at"
          label="Created At" sortable>
        </el-table-column>
        <el-table-column
          label="Actions">
          <template slot-scope="scope">
            <el-button :disabled="user.role != 'admin' && (scope.row.assigned_by_id != user.id && scope.row.assigned_to_id != user.id)" type="text" size="small" @click="editTask(scope.row.id)">Edit</el-button>
            <el-button :disabled="user.role != 'admin' && (scope.row.assigned_by_id != user.id && scope.row.assigned_to_id != user.id)" type="text" size="small" @click="deleteTask(scope.row.id)">Delete</el-button>
            <el-button type="text" size="small" @click="openFilesDialog(scope.row.id)"><i class="el-icon-upload"></i></el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        background
        layout="prev, pager, next"
        style="float: right; padding: 12px"
        :total="total_tasks"
        :page-size="per_page"
        @current-change="loadTasks"
        >
      </el-pagination>

    </el-card>   

    <!-- Add Task Dialog starts -->
    <el-dialog
    title="Add new task"
    :visible.sync="add_task_dialog_show"
    width="30%">
      <el-form ref="form" :model="form" label-width="120px">
        <el-form-item label="Select Project">
          <el-select v-model="form.project_id" placeholder="Select project" filterable>
            <el-option v-for="project in projects" :label="project.name" :value="project.id" :key="project.id"></el-option>
          </el-select>
          <has-error :form="form" field="project_id" />
        </el-form-item>
        <el-form-item label="Assinged by">
          <el-select v-model="form.assigned_by" placeholder="Select Assinged by" filterable>
            <el-option v-for="user in users" :label="user.name" :value="user.id" :key="user.id"></el-option>
          </el-select>
          <has-error :form="form" field="assigned_by" />
        </el-form-item>
        <el-form-item label="Assinged to" v-if="user.role == 'admin'">
          <el-select v-model="form.assigned_to" placeholder="Select Assinged to" filterable>
            <el-option v-for="user in users" :label="user.name" :value="user.id" :key="user.id"></el-option>
          </el-select>
          <has-error :form="form" field="assigned_to" />
        </el-form-item>
        <el-form-item label="Task name">
          <el-input v-model="form.task_name"></el-input>
          <has-error :form="form" field="task_name" />
        </el-form-item>
        <el-form-item label="Task Description">
          <el-input v-model="form.task_desc" type="textarea"></el-input>
          <has-error :form="form" field="task_desc" />
        </el-form-item>
        <el-form-item label="Billing Hours">
          <el-input v-model="form.billing_hours"></el-input>
          <has-error :form="form" field="billing_hours" />
        </el-form-item>
        <el-form-item label="Work Hours">
          <el-input v-model="form.work_hours"></el-input>
          <has-error :form="form" field="work_hours" />
        </el-form-item>
        <el-form-item label="Task status">
          <el-select v-model="form.task_status" placeholder="Select status">
            <el-option label="In Progress" value="in_progress"></el-option>
            <el-option label="Done" value="done"></el-option>
            <el-option label="On hold" value="on_hold"></el-option>
            <el-option label="Closed" value="closed"></el-option>
            <el-option label="Deployed" value="deployed"></el-option>
            <el-option label="Feedback" value="feedback"></el-option>
            <el-option label="Bug" value="bug"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Progress">
           <el-slider
            v-model="form.completion_precentage"
            :step="10"
            show-stops>
          </el-slider>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="form.busy" @click="saveTask">Create</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- Add Task Dialog ends -->

    <!-- Edit Task Dialog starts -->
    <el-dialog
    title="Edit task"
    :visible.sync="edit_task_dialog_show"
    width="30%">
      <el-form ref="edit_form" :model="edit_form" label-width="120px">
        <el-form-item label="Select Project">
          <el-select v-model="edit_form.project_id" placeholder="Select project" filterable>
            <el-option v-for="project in projects" :label="project.name" :value="project.id" :key="project.id"></el-option>
          </el-select>
          <has-error :form="edit_form" field="project_id" />
        </el-form-item>
        <el-form-item label="Assinged by">
          <el-select v-model="edit_form.assigned_by" placeholder="Select Assinged by" filterable>
            <el-option v-for="user in users" :label="user.name" :value="user.id" :key="user.id"></el-option>
          </el-select>
          <has-error :form="edit_form" field="assigned_by" />
        </el-form-item>
        <el-form-item label="Assinged to" v-if="user.role == 'admin'">
          <el-select v-model="edit_form.assigned_to" placeholder="Select Assinged to" filterable>
            <el-option v-for="user in users" :label="user.name" :value="user.id" :key="user.id"></el-option>
          </el-select>
          <has-error :form="edit_form" field="assigned_to" />
        </el-form-item>
        <el-form-item label="Task name">
          <el-input v-model="edit_form.task_name"></el-input>
          <has-error :form="edit_form" field="task_name" />
        </el-form-item>
        <el-form-item label="Task Description">
          <el-input v-model="edit_form.task_desc" type="textarea"></el-input>
          <has-error :form="edit_form" field="task_desc" />
        </el-form-item>
        <el-form-item label="Billing Hours">
          <el-input v-model="edit_form.billing_hours"></el-input>
          <has-error :form="edit_form" field="billing_hours" />
        </el-form-item>
        <el-form-item label="Work Hours">
          <el-input v-model="edit_form.work_hours" :disabled="edit_form.disable_work_hours || edit_form.disable_check"></el-input>
          <has-error :form="edit_form" field="work_hours" />
        </el-form-item>
        <el-form-item label="Task status">
          <el-select v-model="edit_form.task_status" placeholder="Select status">
            <el-option label="In Progress" value="in_progress"></el-option>
            <el-option label="Done" value="done"></el-option>
            <el-option label="On hold" value="on_hold"></el-option>
            <el-option label="Closed" value="closed"></el-option>
            <el-option label="Deployed" value="deployed"></el-option>
            <el-option label="Feedback" value="feedback"></el-option>
            <el-option label="Bug" value="bug"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Progress">
           <el-slider
            v-model="edit_form.completion_precentage"
            :step="10"
            show-stops>
          </el-slider>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="edit_form.busy" @click="updateTask">Update</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- Edit Task Dialog ends -->
    
    <!-- Upload Files Dialog Starts -->
    <el-dialog
      title="Upload Files"
      :visible.sync="add_files_dialog_show" width="30%" center>
      <el-upload
        drag
        action="api/v1/files"
        :file-list="fileList"
        list-type="text"
        :auto-upload="true"
        :on-success="filesUploaded"
        :data="filesData"
        multiple>
        <i class="el-icon-upload"></i>
        <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
      </el-upload>
    </el-dialog>
    <!-- Upload Files Dialog ends -->

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
      return { title: 'Tasks' }
    },
    data() {
      return {
        fileList: [],
        filesData: {
          model_id: '',
          model_name: 'tasks'
        },
        tasks: [],
        projects: [],
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
        current_task: {
          id: null,
          index: null,
        },
        total_tasks: 0,
        current_page: 0,
        per_page: 0,
        table_loading: true,
        add_task_dialog_show: false,
        add_files_dialog_show: false,
        edit_task_dialog_show: false,
        form: new Form({
          project_id: '',
          assigned_by: '',
          assigned_to: '',
          task_name: '',
          completion_precentage: 0,
          task_status: 'in_progress',
          task_desc: '',
          billing_hours: 0,
          work_hours: 0
        }),
        edit_form: new Form({
          project_id: '',
          assigned_by: '',
          assigned_to: '',
          task_name: '',
          completion_precentage: 0,
          task_status: 'in_progress',
          task_desc: '',
          work_hours: '',
          disable_work_hours: false,
          disable_check: false,
          billing_hours: ''
        }),
        filters: {
          user_id: false,
          date: false,
          project_id: 'all'
        }
      }
    },
    mounted() {
        let getFilters = this.$cookie.get('filters');
         if(getFilters) {
          getFilters = JSON.parse(getFilters);
          this.filters.user_id = getFilters.user_id;
          this.filters.date = getFilters.date;
          this.filters.project_id = getFilters.project_id;
         }
        this.loadInitials();
        this.loadTasks(1);
    },
    computed: mapGetters({
      user: 'auth/user'
    }),
    methods: {
      openFilesDialog(id) {
        this.filesData.model_id = id;
        this.add_files_dialog_show = true;
      },
      filesUploaded(response) {
        var self = this;
        self.add_files_dialog_show = false;
        self.fileList = [];
        self.tasks.forEach((task) => {
          if(task.id == this.filesData.model_id) {
            task.files = response.data;
          }
        });
      },
      loadInitials(){
        var self = this;
        var requestData = [{
              url: '/api/v1/projects?all'
            , method: 'get'
            , data: 'some-data'
        }, {
              url: '/api/v1/users?all'
            , method: 'get'
            , data: 'some-other-data'
        }];

        axios.all([
              axios.request(requestData[0]).catch(function() { return false})
            , axios.request(requestData[1]).catch(function() { return false})
        ]).then(axios.spread(function (projects, users) {
            if(projects !== false) {              
              self.projects = projects.data.data;
            }
            if(users !== false) {
              self.users = users.data.data;
            }
        }))
      },
      loadTasks(page) {
       var self = this;
       self.table_loading = true;
       self.current_page = page;       
       this.$cookie.set('filters', JSON.stringify(self.filters));
       axios.get('/api/v1/tasks', {
        params: {
          page: page,
          filters: self.filters
        }
       }).then(function (response) {
              self.total_tasks = parseInt(response.data.meta.total);
              self.per_page = parseInt(response.data.meta.per_page);
              self.tasks = response.data.data;
              self.table_loading = false;
          })
          .catch(function (error) {
              console.log(error);
          });
      },
      async saveTask() {
        var self = this;
        const { data } = await this.form.post('/api/v1/tasks');
        // if(self.form.assigned_by != self.form.assigned_to) {
        //   WS.send(JSON.stringify({
        //     receiver: self.form.assigned_to,
        //     title: self.user.name + ' assigned a new task',
        //     content: self.form.task_name
        //   }));
        // }
        self.tasks.unshift(data);
        self.total_tasks++;
        self.form.assigned_by = '';
        self.form.assigned_to = '';
        self.form.task_desc = '';
        self.form.billing_hours = '';
        self.form.work_hours = '';
        self.form.task_name = '';
        self.form.completion_precentage = 0;
        self.form.task_status = 'in_progress';
        self.add_task_dialog_show = false;
      },
      editTask(id) {
        var self = this;
        self.tasks.forEach((task) => {
          if(task.id == id) {
            self.current_task.id = task.id;
            self.edit_form.project_id = task.project_id;
            self.edit_form.assigned_by = task.assigned_by_id;
            self.edit_form.assigned_by_id = task.assigned_by;
            self.edit_form.assigned_to = task.assigned_to_id;
            self.edit_form.assigned_to_id = task.assigned_to;
            self.edit_form.task_desc = task.task_desc;
            self.edit_form.billing_hours = task.billing_hours;
            self.edit_form.work_hours = task.work_hours;
            self.edit_form.task_name = task.task_name;
            self.edit_form.completion_precentage = task.completion_precentage;
            self.edit_form.task_status = task.task_status; 
            self.edit_form.disable_work_hours = task.disable_work_hours; 
            self.edit_form.disable_check = task.disable_check; 
            self.edit_task_dialog_show = true;
          }
        });
      },
      async updateTask() {
        var self = this;
        const task_id = self.current_task.id;
        const { data } = await this.edit_form.patch('/api/v1/tasks/'+self.current_task.id);
        self.tasks.forEach((task) => {
          if(task.id === task_id) {
            // console.log(data.task_status, 'status ..');
            task.status_formated = data.status_formated;
            task.project_id = data.project_id;
            task.project_name = data.project_name;
            task.assigned_by_id = data.assigned_by_id;
            task.assigned_by = data.assigned_by;
            task.assigned_to = data.assigned_to;
            task.assigned_to_id = data.assigned_to_id;
            task.task_desc = data.task_desc;
            task.task_name = data.task_name;
            task.activity = data.activity;
            task.completion_precentage = data.completion_precentage;
            task.billing_hours = data.billing_hours;
            task.work_hours = data.work_hours;
            task.disable_work_hours = data.disable_work_hours;
            task.disable_check = data.disable_check;
            task.percentage_status = data.percentage_status;
            task.task_status = data.task_status; 
            task.task_status_formated = data.task_status_formated; 
            self.edit_task_dialog_show = false;
          }
        });    
      },
      deleteTask(id) {
        var self = this;
        self.tasks.forEach((task, index) => {
          if(task.id === id) {
            this.$confirm('This will permanently delete the record. Continue?', 'Warning', {
              confirmButtonText: 'OK',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              const { data } = this.edit_form.delete('/api/v1/tasks/'+id);
              self.total_tasks--;
              self.tasks.splice(index, 1);
            });  
          }
        });
      }
    },
  }
</script>
