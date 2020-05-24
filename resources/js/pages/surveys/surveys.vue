<template>
  <div>
    <h1>{{ $t('surveys') }}</h1>
    <SurveysList />
  </div>
</template>

<script>
import SurveysList from '~/components/SurveysList'

export default {
  components: {
    SurveysList
  }
}
</script>
