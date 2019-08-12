<template>

  <div>

    <template>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          {{ locales[locale] }}
        </a>
        <div class="dropdown-menu">
          <a v-for="(value, key) in locales" :key="key" class="dropdown-item" href="#" @click.prevent="setLocale(key)">
            {{ value }}
          </a>
        </div>
      </li>
    </template>


  <template>
    <div class="text-center">
      <v-overlay :value="sheet"></v-overlay>
      <v-bottom-sheet v-model="sheet" inset scrollable>
        <template v-slot:activator="{ on }">
          <v-btn color="orange" dark v-on="on">
            Open Inset
          </v-btn>
        </template>
        <v-sheet class="text-center" scrollable>

          <v-container fluid style="display: inline-block;">
            <v-radio-group v-model="localeRadio" :mandatory="true" @change="setLocale(localeRadio)">
              <v-radio v-for="(value, key) in locales" :label="value" :value="key" :key="key"></v-radio>
            </v-radio-group>
          </v-container>

          <v-btn class="mt-6" text color="error" @click="sheet = !sheet">close</v-btn>
        </v-sheet>
      </v-bottom-sheet>
    </div>
  </template>




  </div>

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

}
</script>
