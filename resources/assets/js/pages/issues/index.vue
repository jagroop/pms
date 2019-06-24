<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix">

        <el-row :gutter="20">
          <el-col :span="4">
            <div class="grid-content">
              <span>Issues</span>
            </div>
          </el-col>
          <el-col :span="16">
            <div class="grid-content">
              <div class="filters">
                <el-form :inline="true" label-width="120px">
                  <el-form-item label="Filters : " prop="filters" style="margin:0;">
                      <el-checkbox @change="loadIssues(1)" label="Assinged to me" v-model="filters.user_id" name="user_id"></el-checkbox>
                      <el-checkbox @change="loadIssues(1)" label="Today" v-model="filters.date" name="date"></el-checkbox>                      
                  </el-form-item>

                  <el-form-item prop="filters" style="margin-left: 18px;">
                      <el-select @change="loadIssues(1)" v-model="filters.project_id" filterable placeholder="Select Project">
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
              <el-button style="float: right; padding: 3px 0" type="text" @click="add_issue_dialog_show = true">Add Issue</el-button>              
            </div>
          </el-col>
        </el-row>        
        
      </div>
      <el-table
      :data="issues"
      :default-sort = "{prop: 'created_at', order: 'descending'}"
      v-loading="table_loading"
      style="width: 100%">
        <el-table-column type="expand">
          <template slot-scope="props">
            <p>Description: {{ props.row.issue_desc }}</p>
            <p>Assigned By: {{ props.row.assigned_by }}</p>
            <ul>
                <li v-for="(file, index) in props.row.files" :key="index"><a :href="file.full_url" target="_blank">{{ file.file_name }}</a> | {{ file.created_at }}</li>
            </ul>
          </template>
        </el-table-column>
        <el-table-column
          prop="id"
          label="Issue ID"
          sortable>
        </el-table-column>
        <el-table-column
          prop="project_name"
          label="Project Name"
          sortable>
        </el-table-column>
        <el-table-column
          prop="issue_name"
          label="Issue Name"
          sortable>
        </el-table-column>
        <el-table-column
          prop="status_formated"
          label="Status" sortable>
          <template slot-scope="scope">
            <el-tag
              :type="statuses[scope.row.issue_status]"
              disable-transitions>{{scope.row.issue_status_formated}}</el-tag>
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
            <el-button :disabled="user.role != 'admin' && (scope.row.assigned_by_id != user.id && scope.row.assigned_to_id != user.id)" type="text" size="small" @click="editIssue(scope.row.id)">Edit</el-button>
            <el-button :disabled="user.role != 'admin' && (scope.row.assigned_by_id != user.id && scope.row.assigned_to_id != user.id)" type="text" size="small" @click="deleteIssue(scope.row.id)">Delete</el-button>
            <el-button type="text" size="small" @click="openFilesDialog(scope.row.id)"><i class="el-icon-upload"></i></el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        background
        layout="prev, pager, next"
        style="float: right; padding: 12px"
        :total="total_issues"
        :page-size="per_page"
        @current-change="loadIssues"
        >
      </el-pagination>

    </el-card>   

    <!-- Add Issue Dialog starts -->
    <el-dialog
    title="Add new issue"
    :visible.sync="add_issue_dialog_show"
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
        <el-form-item label="Assinged to">
          <el-select v-model="form.assigned_to" placeholder="Select Assinged to" filterable>
            <el-option v-for="user in users" :label="user.name" :value="user.id" :key="user.id"></el-option>
          </el-select>
          <has-error :form="form" field="assigned_to" />
        </el-form-item>
        <el-form-item label="Issue name">
          <el-input v-model="form.issue_name"></el-input>
          <has-error :form="form" field="issue_name" />
        </el-form-item>
        <el-form-item label="Issue Description">
          <el-input v-model="form.issue_desc" type="textarea"></el-input>
          <has-error :form="form" field="issue_desc" />
        </el-form-item>
        <el-form-item label="Issue status">
          <el-select v-model="form.issue_status" placeholder="Select status">
            <el-option label="In Progress" value="in_progress"></el-option>
            <el-option label="Done" value="done"></el-option>
            <el-option label="Fixed" value="fixed"></el-option>
            <el-option label="Resolved" value="resolved"></el-option>
            <el-option label="On hold" value="on_hold"></el-option>
            <el-option label="Closed" value="closed"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="form.busy" @click="saveIssue">Create</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- Add Issue Dialog ends -->

    <!-- Edit Issue Dialog starts -->
    <el-dialog
    title="Edit issue"
    :visible.sync="edit_issue_dialog_show"
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
        <el-form-item label="Assinged to">
          <el-select v-model="edit_form.assigned_to" placeholder="Select Assinged to" filterable>
            <el-option label="All" value="all"></el-option>
            <el-option v-for="user in users" :label="user.name" :value="user.id" :key="user.id"></el-option>
          </el-select>
          <has-error :form="edit_form" field="assigned_to" />
        </el-form-item>
        <el-form-item label="Issue name">
          <el-input v-model="edit_form.issue_name"></el-input>
          <has-error :form="edit_form" field="issue_name" />
        </el-form-item>
        <el-form-item label="Issue Description">
          <el-input v-model="edit_form.issue_desc" type="textarea"></el-input>
          <has-error :form="edit_form" field="issue_desc" />
        </el-form-item>
        <el-form-item label="Issue status">
          <el-select v-model="edit_form.issue_status" placeholder="Select status">
            <el-option label="In Progress" value="in_progress"></el-option>
            <el-option label="Done" value="done"></el-option>
            <el-option label="On hold" value="on_hold"></el-option>
            <el-option label="Closed" value="closed"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="edit_form.busy" @click="updateIssue">Update</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- Edit Issue Dialog ends -->
    
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
      return { title: 'Issues' }
    },
    data() {
      return {
        fileList: [],
        filesData: {
          model_id: '',
          model_name: 'issues'
        },
        issues: [],
        projects: [],
        users: [],
        statuses: {
          in_progress: 'success',
          done: 'primary',
          on_hold: 'danger',
        },
        current_issue: {
          id: null,
          index: null,
        },
        total_issues: 0,
        current_page: 0,
        per_page: 0,
        table_loading: true,
        add_files_dialog_show: false,
        add_issue_dialog_show: false,
        edit_issue_dialog_show: false,
        form: new Form({
          project_id: '',
          assigned_by: '',
          assigned_to: '',
          issue_name: '',
          issue_status: 'in_progress',
          issue_desc: ''
        }),
        edit_form: new Form({
          project_id: '',
          assigned_by: '',
          assigned_to: '',
          issue_name: '',
          issue_status: 'in_progress',
          issue_desc: ''
        }),
        filters: {
          user_id: false,
          date: false,
          project_id: 'all'
        }
      }
    },
    mounted() {
        let getFilters = this.$cookie.get('issues_filters');
         if(getFilters) {
          getFilters = JSON.parse(getFilters);
          this.filters.user_id = getFilters.user_id;
          this.filters.date = getFilters.date;
          this.filters.project_id = getFilters.project_id;
         }
        this.loadInitials();
        this.loadIssues(1);
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
        self.issues.forEach((issue) => {
          if(issue.id == this.filesData.model_id) {
            issue.files = response.data;
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
      loadIssues(page) {
       var self = this;
       self.table_loading = true;
       self.current_page = page;    
       console.log(self.filters);   
       this.$cookie.set('issues_filters', JSON.stringify(self.filters));
       axios.get('/api/v1/issues', {
        params: {
          page: page,
          filters: self.filters
        }
       }).then(function (response) {
              self.total_issues = parseInt(response.data.meta.total);
              self.per_page = parseInt(response.data.meta.per_page);
              self.issues = response.data.data;
              self.table_loading = false;
          })
          .catch(function (error) {
              console.log(error);
          });
      },
      async saveIssue() {
        var self = this;
        const { data } = await this.form.post('/api/v1/issues');
        self.issues.unshift(data);
        
        // if(self.form.assigned_by != self.form.assigned_to) {
        //   WS.send(JSON.stringify({
        //     receiver: self.form.assigned_to,
        //     title: self.user.name + ' assigned you issue',
        //     content: self.form.issue_name
        //   }));
        // }

        self.total_issues++;
        self.form.assigned_by = '';
        self.form.assigned_to = '';
        self.form.issue_desc = '';
        self.form.issue_name = '';
        self.form.issue_status = 'in_progress';
        self.add_issue_dialog_show = false;
      },
      editIssue(id) {
        var self = this;
        self.issues.forEach((issue) => {
          if(issue.id == id) {
            self.current_issue.id = issue.id;
            self.edit_form.project_id = issue.project_id;
            self.edit_form.assigned_by = issue.assigned_by_id;
            self.edit_form.assigned_by_id = issue.assigned_by;
            self.edit_form.assigned_to = issue.assigned_to_id;
            self.edit_form.assigned_to_id = issue.assigned_to;
            self.edit_form.issue_desc = issue.issue_desc;
            self.edit_form.issue_name = issue.issue_name;
            self.edit_form.issue_status = issue.issue_status; 
            self.edit_issue_dialog_show = true;
          }
        });
      },
      async updateIssue() {
        var self = this;
        const issue_id = self.current_issue.id;
        const { data } = await this.edit_form.patch('/api/v1/issues/'+self.current_issue.id);
        self.issues.forEach((issue) => {
          if(issue.id === issue_id) {
            // if(issue.issue_status != data.issue_status) {
            //   WS.send(JSON.stringify({
            //     receiver: data.assigned_to,
            //     title: self.user.name + ' updated issue #' + data.id,
            //     content: issue.issue_status_formated + ' -> ' + data.issue_status_formated
            //   }));
            // }
            issue.status_formated = data.status_formated;
            issue.project_id = data.project_id;
            issue.project_name = data.project_name;
            issue.assigned_by_id = data.assigned_by_id;
            issue.assigned_by = data.assigned_by;
            issue.assigned_to = data.assigned_to;
            issue.assigned_to_id = data.assigned_to_id;
            issue.issue_desc = data.issue_desc;
            issue.issue_name = data.issue_name;
            issue.issue_status = data.issue_status; 
            issue.issue_status_formated = data.issue_status_formated; 
            self.edit_issue_dialog_show = false;
          }
        });    
      },
      deleteIssue(id) {
        var self = this;
        self.issues.forEach((issue, index) => {
          if(issue.id === id) {
            this.$confirm('This will permanently delete the record. Continue?', 'Warning', {
              confirmButtonText: 'OK',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              const { data } = this.edit_form.delete('/api/v1/issues/'+id);
              self.total_issues--;
              self.issues.splice(index, 1);
            });  
          }
        });
      }
    },
  }
</script>
