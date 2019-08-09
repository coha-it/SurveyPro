<template>
    <div style="padding: 25px;">
        <h1>Willkommen</h1>
        <p style="max-width: 400px;">Um sich in ihr SurveyPro-Konto anzumelden, klicken Sie auf eine der drei unterstehenden Optionen</p>

        <router-link :to="{ name: 'login' }" class="small ml-auto my-auto">
          {{ $t('login') }}
        </router-link>

    </div>
</template>

<script>
export default {
  
  middleware: 'guest',

  layout: 'rightsided',

  metaInfo () {
    return { title: this.$t('authentificate') }
  },

}
</script>

<style lang="scss">

</style>
