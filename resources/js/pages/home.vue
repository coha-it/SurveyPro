<template>
  <div class="main-layout">
    <h1>AGA {{$t('home')}}</h1>
    asdf
    <p>{{ $t('you_are_logged_in') }}</p>
    <p v-for="i in range(20)" v-bind:key="i">{{ $t('you_are_logged_in') }}</p>
  </div>
</template>

<script>
export default {
  middleware: 'auth',

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
