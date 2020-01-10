<template>
  <div class="main-layout">
    <h1>{{$t('home')}}</h1>
    <SurveysList />
  </div>
</template>

<script>
import SurveysList from '~/components/SurveysList'

export default {
  middleware: 'auth',
  components: {
    SurveysList
  },
  // This was causing Theme-Problems
  // metaInfo () {
  //   return { title: this.$t('home') }
  // },

  methods: {
    range: function(c) {
      return new Array(c);
    }
  }

}
</script>
