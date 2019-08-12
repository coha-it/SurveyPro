<template>
  <span data-app>
    <template>
        <v-overlay :value="sheet"></v-overlay>
        <v-bottom-sheet v-model="sheet" scrollable>
          <template v-slot:activator="{ on }">
            <a href="#" v-on="on" >{{ $t(locales[locale]) }}</a>
          </template>
          <v-sheet class="text-center" scrollable>
            <v-container fluid>
              <v-radio-group style="display: inline-block;" v-model="localeRadio" :mandatory="true" @change="setLocale(localeRadio)">
                <v-radio v-for="(value, key) in locales" :label="$t(value)" :value="key" :key="key"></v-radio>
              </v-radio-group>
            </v-container>
            <v-btn class="mt-6" text color="error" @click="sheet = !sheet">{{ $t('closer_button') }}</v-btn>
          </v-sheet>
        </v-bottom-sheet>
    </template>
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
  },

  data () {
    return {
      dialogm1: '',
      dialog: false,
      localeRadio: 'de',
      sheet: false,
    }
  },

  mounted() {
    if(this.locale) {
      this.localeRadio = this.locale;
    }
  }

}
</script>
