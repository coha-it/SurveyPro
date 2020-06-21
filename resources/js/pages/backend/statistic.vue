<template lang="pug">
div
  h1 Statistics
  q-btn(label="reload" @click="getSurveyStatistics()")
  div.code.c_code(style='max-height: unset;') {{ surveyStats }}
</template>
<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {

  data () {
    return {
      ids: [],
      surveyStats: null
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
      this.getSurveyStatistics(this.ids)
    }
  },

  methods: {
    initUrlQuerys () {
      try {
        this.ids = this.$route.query.ids
      } catch {
        alert('Error - no IDs')
      }
    },

    getSurveyStatistics (ids = this.ids) {
      axios
        .post('/api/backend/surveys-statistics', { ids: ids })
        .then((res) => {
          this.surveyStats = res.data
        })
    }
  }

}
</script>
