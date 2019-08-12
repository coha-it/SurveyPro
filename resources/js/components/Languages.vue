<template>

  <div>

    <template>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button"
          data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        >
          {{ locales[locale] }}
        </a>
        <div class="dropdown-menu">
          <a v-for="(value, key) in locales" :key="key" class="dropdown-item" href="#"
            @click.prevent="setLocale(key)"
          >
            {{ value }}
          </a>
        </div>
      </li>
    </template>


<template>
  <v-container fluid>
    <p>{{ radios || 'null' }}</p>
    <v-radio-group v-model="radios" :mandatory="false">
      <v-radio label="Radio 1" value="radio-1"></v-radio>
      <v-radio label="Radio 2" value="radio-2"></v-radio>
    </v-radio-group>
  </v-container>
</template>





  </div>

</template>





<script>
import { mapGetters } from 'vuex'
import { loadMessages } from '~/plugins/i18n'

export default {

  computed: mapGetters({
    locale: 'lang/locale',
    locales: 'lang/locales'
  }),

  methods: {
    setLocale (locale) {
      if (this.$i18n.locale !== locale) {
        loadMessages(locale)

        this.$store.dispatch('lang/setLocale', { locale })
      }
    }
  },

  data () {
    return {
      dialogm1: '',
      dialog: false,
      radios: 'radios-1'
    }
  },

}
</script>
