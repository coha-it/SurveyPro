<template lang="pug">
  .row.items-center.justify-end.q-gutter-sm
    q-btn(v-close-popup, v-if='!oSurvey.active', :label="$t('remove')", color='primary', outline, unelevated, size='md', @click='oSurvey[sModel] = null')
    q-space
    q-btn(v-close-popup, :label="$t('ok')", color='primary', unelevated)
</template>

<script>
export default {
  props: {
    sModel: {
      type: String,
      required: true
    },
    oSurvey: {
      type: Object,
      required: true
    }
  }
}
</script>
