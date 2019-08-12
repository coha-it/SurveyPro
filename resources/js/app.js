import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'

import '~/plugins'
import '~/components'

// Import Vuetify
import Vuetify from 'vuetify'
Vue.use(Vuetify, {
  icons: {
    iconfont: 'mdi'
  }
})

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  i18n,
  store,
  router,
  Vuetify,
  render: h => h(App),
  ...App
})
