import Vue from 'vue'
import ElementUI from 'element-ui'
import VueCookie from 'vue-cookie'
import locale from 'element-ui/lib/locale/lang/en'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'

import '~/plugins'
import '~/components'
import 'element-ui/lib/theme-chalk/index.css'

Vue.config.productionTip = false
Vue.use(ElementUI, { locale })
Vue.use(VueCookie)


// window.WS = new WebSocket('ws://192.168.5.254:8090');

/* eslint-disable no-new */

new Vue({
  i18n,
  store,
  router,
  ...App
})
