<template>
  <el-menu
  :default-active="activeIndex"
  class="el-menu-demo"
  mode="horizontal"
  @select="handleSelect"
  background-color="#545c64"
  text-color="#fff"
  active-text-color="#ffd04b">
  <el-menu-item index="1">{{ appName }}</el-menu-item>
</el-menu>
</template>

<script>
  export default {
    data() {
      return {
        activeIndex: '1',
        appName: window.config.appName
      };
    },
    methods: {
      handleSelect(key, keyPath) {
        console.log(key, keyPath);
      }
    }
  }
</script>