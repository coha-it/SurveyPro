<template>
  <div class="main-layout">
    <h1>{{ $t('home') }}</h1>
    <h6 class="q-my-sm" header>Ihre Umfragen</h6>
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

  data: function () {
    return {}
  },

  // This was causing Theme-Problems
  // metaInfo () {
  //   return { title: this.$t('home') }
  // },

  mounted: function () {}
}
</script>
