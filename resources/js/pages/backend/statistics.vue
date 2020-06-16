<template lang="pug">
div
  h1 Statistics
  div(style='max-width: 770px')
    div.row.items-center
      div
        q-input(v-model='search' debounce='500' filled label='Umfrage suchen' placeholder='Search' hint='Debouncing 500ms')
          template(v-slot:append='')
            q-icon(name='search')

    q-list(v-for="survey in surveys")
      q-item
        q-item-section(avatar)
          | {{ survey.id }}
        q-item-section
          q-item-label(v-html='survey.title')
          q-item-label(caption) {{ survey.desc_long }}
          q-item-label(caption) {{ survey.desc_short }}
</template>
<script>
/* eslint-disable eqeqeq */
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {

  data () {
    return {
      search: null,
      surveys: []
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  },

  watch: {
    search: {
      handler (after, before) {
        if (
          (after ? after.toLowerCase() : '') !== (before ? before.toLowerCase() : '')
        ) {
          this.loadFilteredSurveys(after)
        }
      }
    }
  },

  created: function () {
    this.$store.dispatch('surveys/fetchBackendSurveysAllowed')
    this.loadFilteredSurveys()
  },

  methods: {
    loadFilteredSurveys (val = '') {
      axios
        .post('/api/backend/surveys-allowed-filtered', { search: val.toLowerCase() })
        .then((res) => {
          this.surveys = Object.values(res.data)
        })
    }
  }

}
</script>
