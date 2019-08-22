<template>
  <div class="main-layout">
    <h1>{{$t('home')}}</h1>
    <p>{{ $t('you_are_logged_in') }}</p>
  </div>
</template>

<script>
export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('home') }
  },

}
</script>
