<template>
  <span data-app>
    <a href="#" @click="show()" >{{ $t(locales[locale]) }}</a>
  </span>
</template>

<script>
import { mapGetters } from 'vuex'
import { mapActions } from 'vuex'
import { loadMessages } from '~/plugins/i18n'

export default {

  computed: {
    ...mapGetters({
      locale: 'lang/locale',
      locales: 'lang/locales',
    }),
  },

  methods: {
    setLocale (locale) {
      if (this.$i18n.locale !== locale) {
        loadMessages(locale)

        this.$store.dispatch('lang/setLocale', { locale })
      }
    },
    show () {
      this.$q.bottomSheet({
        message: 'Sprache auswählen',
        actions: this.aLocaleOptions,
      }).onOk(action => {
        this.setLocale(action.id);
      }).onCancel(() => {
        // console.log('Dismissed')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
  },

  data () {
    return {
      dialogm1: '',
      dialog: false,
      localeRadio: 'de',
      aLocaleOptions: [],
      sheet: false,
    }
  },

  mounted() {
    var _t = this;

    if(this.locale) {
      this.localeRadio = this.locale;

      for (var element in this.locales) {
        this.aLocaleOptions.push({
          label: _t.$t(element),
          id: element
        })
      }
    }
  }

}
</script>
