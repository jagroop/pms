<template>
  <div>
    <navbar/>
  <div class="main-layout">
    <el-container>
      <el-aside width="200px">
        <!-- Aside starts -->
        <el-menu 
          :default-active="current_active_tab" 
          class="el-menu-vertical-demo" 
          @select="switchTab"
          background-color="#545c64"
          text-color="#fff"
          active-text-color="#ffd04b"
          >          
          <el-menu-item index="home">
            <i class="el-icon-news"></i>
            <span slot="title">Dashboard</span>
          </el-menu-item>
          <el-menu-item index="projects">
            <i class="el-icon-tickets"></i>
            <span slot="title">Projects</span>
          </el-menu-item>
          <el-menu-item index="tasks">
            <i class="el-icon-circle-check-outline"></i>
            <span slot="title">Tasks</span>
          </el-menu-item>
          <el-menu-item index="issues">
            <i class="el-icon-info"></i>
            <span slot="title">Issues</span>
          </el-menu-item>
          <el-menu-item index="users">
            <i class="el-icon-location"></i>
            <span slot="title">Users</span>
          </el-menu-item>
          <el-menu-item index="logs">
            <i class="el-icon-edit-outline"></i>
            <span slot="title">Logs</span>
          </el-menu-item>
        </el-menu>
        <!-- Aside ends -->
      </el-aside>
      <el-main>
        <child/>
      </el-main>
    </el-container>
  </div>
  </div>
</template>
<script>
import Navbar from '~/components/HomeNavbar'
import { mapGetters } from 'vuex'

export default {
  name: 'AppLayout',

  components: {
    Navbar
  },
  data() {
    return {
      current_active_tab: 'home'
    }
  },
  computed: mapGetters({
    user: 'auth/user'
  }),
  mounted() {
    //
  },
  methods: {
    switchTab(index) {
      this.current_active_tab = index;
      this.$router.push({ name: index })
    }
  }
}
</script>
<style>
  .invalid-feedback {
    display: block;
  }
</style>
