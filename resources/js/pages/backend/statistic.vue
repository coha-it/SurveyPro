<template lang="pug">
div
  h1 Statistics
  q-btn(label="stats 1" unelevated color="primary" outline @click="getSurveyStatistics(1)")
  | &nbsp;
  q-btn(label="stats 2" unelevated color="primary" outline @click="getSurveyStatistics(2)")
  div(v-if="stats")
    span(v-for='head in stats.header') {{ head }};
    div(v-for='(tr, i) in stats.data')
      span {{ i }}
      span(v-for='td in tr') {{ td }};
  template(v-if='stats && stats.surveys')
    div(v-for="survey in stats.surveys")
      q-table(
        :columns="survey.questions"
        :data="survey.awnsers"
        :pagination="pagination"
        dense
      )
  q-markup-table(v-if="stats" dense cell)
    thead
      tr
        th
        th(v-for='head in stats.header') {{ head }}
    tbody
      tr(v-for='(tr, i) in stats.data')
        td {{ i }}
        td(v-for='td in tr') {{ td }}
  div.code.c_code(style='max-height: unset;') {{ stats }}
</template>
<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {

  data () {
    return {
      ids: [],
      stats: null,
      pagination: {
        sortBy: 'desc',
        descending: false,
        page: 2,
        rowsPerPage: null
        // rowsNumber: xx if getting data from a server
      }
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  },

  mounted: function () {
    this.initUrlQuerys()

    if (this.ids) {
      this.getSurveyStatistics(2, this.ids)
    }
  },

  methods: {
    log (data) {
      console.log(data)
    },
    initUrlQuerys () {
      try {
        this.ids = this.$route.query.ids
      } catch {
        alert('Error - no IDs')
      }
    },

    getSurveyStatistics (type = 1, ids = this.ids) {
      axios
        .post('/api/backend/surveys-statistics', {
          type: type,
          ids: ids
        })
        .then((res) => {
          this.stats = res.data
        })
    }
  }

}
</script>
