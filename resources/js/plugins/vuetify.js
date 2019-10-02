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
        primary: '#54656C',
        secondary: '#9AC0C9',
        accent: '#E8D03E',
        error: '#dc6161', //#C9876E
        info: '#C6C6C6',
        success: '#6CAE75',
        green: '#55a559',
        red: '#cf6035',
        orange: '#cfae35',
        warning: '#E6BE72',
        background: '#fafafa'
      }
    }
  }
})
