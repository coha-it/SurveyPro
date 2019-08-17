import Vue from 'vue'
import Vuetify from 'vuetify'

Vue.use(Vuetify)

export default new Vuetify({
  icons: {
    iconfont: 'mdi'
  },
  options: {
    customProperties: true
  },
  theme: {
    themes: {
      light: {
        primary: '#E8D03E',
        secondary: '#9AC0C9',
        accent: '#54656C',
        error: '#dc6161', //#C9876E
        info: '#C6C6C6',
        success: '#6CAE75',
        warning: '#E6BE72',
        background: '#fafafa'
      }
    }
  }
})
