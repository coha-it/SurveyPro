<template>
  <SurveyDetails
    v-if="oSurvey"
    :o-survey="oSurvey"
  />
</template>

<script>
import { mapGetters } from 'vuex'
import SurveyDetails from '~/components/SurveyDetails'

export default {
  layout: 'NakedLayout',
  middleware: 'auth',

  components: {
    SurveyDetails
  },

  data () {
    return {
      oSurvey: null
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      surveyFillable: 'surveys/surveyFillable'
    })
  },

  watch: {
    surveyFillable: function (promise) {
      this.oSurvey = this.copyObject(promise)
    }
  },

  created: function () {
    this.$store.dispatch(
      'surveys/fetchSurveyFillable',
      parseInt(this.$route.params.id)
    )
  },

  methods: {
    copyObject (obj) {
      if (typeof obj !== 'undefined') {
        var copy = JSON.parse(JSON.stringify(obj))
        if (copy) return copy
      }
    }
  }

}
</script>
