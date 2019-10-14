<template>
    <div class="rightsided-content">
      <div class="inner-content">
        <h1>Willkommen</h1>
        <p style="max-width: 400px;">Um sich in ihr SurveyPro-Konto anzumelden, klicken Sie auf eine der drei unterstehenden Optionen</p>

        <div style="max-width: 500px;">
          
          <v-subheader class="divider"><span>{{ $t('pan_and_pin') }}</span></v-subheader>
          <AuthPanel 
            :title="$t('pan_title')"
            :text="$t('pan_desc')" 
            :route="'pan'"
            :color="'accent'" 
            :icon="'mdi-dialpad'"
            />

          <v-subheader class="divider"><span>{{ $t('with_email') }}</span></v-subheader>
          <AuthPanel 
            :title="$t('register')"
            :text="$t('register_desc')" 
            :route="'register'"
            :color="'secondary'" 
            :icon="'mdi-account-plus'"
            />
          <AuthPanel 
            :title="$t('login')"
            :text="$t('login_desc')" 
            :route="'login'"
            :color="'info'" 
            :icon="'mdi-account'"
            />

        </div>
      </div>
    </div>
</template>

<script>

import AuthPanel from '~/components/AuthPanel';

export default {
  
  middleware: 'guest',
  layout: 'rightsided',

  components: {
    AuthPanel
  },

  // This was causing Theme-Problems
  // metaInfo () {
  //   return { title: this.$t('authentificate_title') }
  // },

}
</script>

<style lang="scss">

</style>
