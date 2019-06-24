<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span>Projects</span>
        <el-button v-if="user.role == 'admin'" style="float: right; padding: 3px 0" type="text" @click="add_project_dialog_show = true">Add Project</el-button>
      </div>
      <el-table
      :data="projects"
      :default-sort = "{prop: 'created_at', order: 'descending'}"
      v-loading="table_loading"
      style="width: 100%">
        <el-table-column type="expand">
          <template slot-scope="props">
            <ul>
                <li v-for="(file, index) in props.row.files" :key="index"><a :href="file.full_url" target="_blank">{{ file.file_name }}</a> | {{ file.created_at }}</li>
            </ul>          
          </template>
        </el-table-column>
        <el-table-column
          prop="name"
          label="Name"
          sortable>
        </el-table-column>
        <el-table-column
          prop="billing_hours"
          label="Billing Hours"
          sortable>
        </el-table-column>
        <el-table-column
          prop="work_hours"
          label="Work Hours"
          sortable>
        </el-table-column>
        <el-table-column
          prop="status_formated"
          label="Status" sortable>
          <template slot-scope="scope">
            <el-tag
              :type="statuses[scope.row.status]"
              disable-transitions>{{scope.row.status_formated}}</el-tag>
          </template>
        </el-table-column>
        <el-table-column
          prop="started_date"
          label="Started At">
        </el-table-column>
        <el-table-column
          prop="closed_date"
          label="Closed At">
        </el-table-column>
        <el-table-column
          prop="created_at"
          label="Created At">
        </el-table-column>
        <el-table-column
          v-if="user.role == 'admin'"
          label="Actions">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="editProject(scope.row.id)">Edit</el-button>
            <el-button type="text" size="small" @click="deleteProject(scope.row.id)">Delete</el-button>
            <el-button type="text" size="small" @click="openFilesDialog(scope.row.id)"><i class="el-icon-upload"></i></el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        background
        layout="prev, pager, next"
        style="float: right; padding: 12px"
        :total="total_projects"
        :page-size="per_page"
        @current-change="loadProjects"
        >
      </el-pagination>

    </el-card>   

    <!-- Add Project Dialog starts -->
    <el-dialog
    title="Add new project"
    :visible.sync="add_project_dialog_show"
    width="30%">
      <el-form ref="form" :model="form" label-width="120px">
        <el-form-item label="Project name">
          <el-input v-model="form.name"></el-input>
          <has-error :form="form" field="name" />
        </el-form-item>
        <el-form-item label="Project status">
          <el-select v-model="form.status" placeholder="Select status">
            <el-option label="In Progress" value="in_progress"></el-option>
            <el-option label="Done" value="done"></el-option>
            <el-option label="On hold" value="on_hold"></el-option>
            <el-option label="Closed" value="closed"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Start Date">
          <el-date-picker
            v-model="form.started_date"
            type="datetime"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="Select start date and time">
          </el-date-picker>
          <has-error :form="form" field="started_date" />
        </el-form-item>
        <el-form-item label="Closed Date">
          <el-date-picker
            v-model="form.closed_date"
            type="datetime"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="Select closed date and time">
          </el-date-picker>
          <has-error :form="form" field="closed_date" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="form.busy" @click="saveProject">Create</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- Add Project Dialog ends -->

    <!-- Edit Project Dialog starts -->
    <el-dialog
    title="Edit project"
    :visible.sync="edit_project_dialog_show"
    width="30%">
      <el-form ref="edit_form" :model="edit_form" label-width="120px">
        <el-form-item label="Project name">
          <el-input v-model="edit_form.name"></el-input>
          <has-error :form="edit_form" field="name" />
        </el-form-item>
        <el-form-item label="Project status">
          <el-select v-model="edit_form.status" placeholder="Select status">
            <el-option label="In Progress" value="in_progress"></el-option>
            <el-option label="Done" value="done"></el-option>
            <el-option label="On hold" value="on_hold"></el-option>
            <el-option label="Closed" value="closed"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Start Date">
          <el-date-picker
            v-model="edit_form.started_date"
            type="datetime"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="Select start date and time">
          </el-date-picker>
          <has-error :form="edit_form" field="started_date" />
        </el-form-item>
        <el-form-item label="Closed Date">
          <el-date-picker
            v-model="edit_form.closed_date"
            type="datetime"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="Select closed date and time">
          </el-date-picker>
          <has-error :form="edit_form" field="closed_date" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="edit_form.busy" @click="updateProject">Update</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- Edit Project Dialog ends -->
    
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
      return { title: 'Projects' }
    },
    data() {
      return {
        projects: [],
        fileList: [],
        filesData: {
          model_id: '',
          model_name: 'projects'
        },
        statuses: {
          in_progress: 'success',
          done: 'primary',
          on_hold: 'danger',
        },
        current_project: {
          id: null,
          index: null,
        },
        total_projects: 0,
        per_page: 0,
        table_loading: true,
        add_project_dialog_show: false,
        add_files_dialog_show: false,
        edit_project_dialog_show: false,
        form: new Form({
          name: '',
          status: 'in_progress',
          started_date: '',
          closed_date: '',
        }),
        edit_form: new Form({
          name: '',
          status: 'in_progress',
          started_date: '',
          closed_date: '',
        }),
      }
    },
    mounted() {
        this.loadProjects(1);
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
        self.projects.forEach((project) => {
          if(project.id == this.filesData.model_id) {
            project.files = response.data;
          }
        });
      },
      loadProjects(page) {
       var self = this;
       self.table_loading = true;
       axios.get('/api/v1/projects?page='+page)
          .then(function (response) {
              self.total_projects = parseInt(response.data.meta.total);
              self.per_page = parseInt(response.data.meta.per_page);
              self.projects = response.data.data;
              self.table_loading = false;
          })
          .catch(function (error) {
              console.log(error);
          });
      },
      async saveProject() {
        var self = this;
        const { data } = await this.form.post('/api/v1/projects');
        self.projects.unshift(data);
        self.total_projects++;
        self.form.name = '';
        self.form.status = 'in_progress';
        self.add_project_dialog_show = false;
      },
      editProject(id) {
        var self = this;

        self.projects.forEach((project) => {
          if(project.id == id) {
            self.current_project.id = project.id;
            self.edit_form.name = project.name;
            self.edit_form.status = project.status;
            self.edit_form.started_date = project.started_date;
            self.edit_form.closed_date = project.closed_date;
            self.edit_project_dialog_show = true;
          }
        });
      },
      async updateProject() {
        var self = this;
        const project_id = self.current_project.id;
        const { data } = await this.edit_form.patch('/api/v1/projects/'+project_id);
        
        self.projects.forEach((project) => {
          if(project.id == project_id) {
            project.name = data.name;
            project.status_formated = data.status_formated;
            project.status = data.status;
            project.started_date = data.started_date;
            project.closed_date = data.closed_date;
            self.edit_project_dialog_show = false;
          }
        });
      },
      deleteProject(id) {
        var self = this;
        self.projects.forEach((project, index) => {
          if(project.id === id) {
            this.$confirm('This will permanently delete the record. Continue?', 'Warning', {
              confirmButtonText: 'OK',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              const { data } = this.edit_form.delete('/api/v1/projects/'+id);
              self.total_projects--;
              self.projects.splice(index, 1);
            });  
          }
        });
      }
    },
  }
</script>