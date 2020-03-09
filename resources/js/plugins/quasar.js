import Vue from 'vue'
import Quasar, { colors } from 'quasar'

colors.setBrand('primary', '#54656C')
colors.setBrand('secondary', '#9AC0C9')
colors.setBrand('accent', '#E8D03E')
colors.setBrand('error', '#dc6161')
colors.setBrand('negative', '#dc6161')
colors.setBrand('info', '#C6C6C6')
colors.setBrand('success', '#55a559')
colors.setBrand('green', '#55a559') // #6CAE75
colors.setBrand('positive', '#55a559') // #6CAE75
colors.setBrand('red', '#cf6035')
colors.setBrand('orange', '#cfae35')
colors.setBrand('warning', '#E6BE72')
colors.setBrand('background', '#fafafa')

export default Vue.use(Quasar, {
  framework: {
    iconSet: 'mdi-v4',
    config: {
      brand: {
        primary: 'red'
      }
    }
  }
})
