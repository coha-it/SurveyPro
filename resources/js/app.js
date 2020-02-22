import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'
import MyQuasar from '~/plugins/quasar'
import VueMoment from 'vue-moment'

import '~/plugins'
import '~/components'

// Some Config Settings
Vue.config.productionTip = false

// Vue Moment
Vue.use(VueMoment)

/* eslint-disable no-new */
new Vue({
  i18n,
  store,
  router,
  // vuetify,
  MyQuasar,
  render: h => h(App),
  ...App
}).$mount('#app')
