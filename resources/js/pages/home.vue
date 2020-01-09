<template>
  <div class="main-layout">
    <h1>{{$t('home')}}</h1>
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
