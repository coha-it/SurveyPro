<template>
    <div style="padding: 25px;">
        <h1>Willkommen</h1>
        <p style="max-width: 400px;">Um sich in ihr SurveyPro-Konto anzumelden, klicken Sie auf eine der drei unterstehenden Optionen</p>

        <router-link :to="{ name: 'login' }">
          {{ $t('login') }}
        </router-link>

        <div style="max-width: 500px;">
          
          <v-subheader class="divider"><span>{{ $t('pin_and_pan') }}</span></v-subheader>
          <AuthPanel 
            :title="$t('pin_and_pan')"
            :text="$t('pin_and_pan_desc')" 
            :route="'pin'"
            :color="'primary'" 
            />

          <v-subheader class="divider"><span>{{ $t('with_email') }}</span></v-subheader>
          <AuthPanel 
            :title="$t('register')"
            :text="$t('register_desc')" 
            :route="'register'"
            :color="'secondary'" 
            />
          <AuthPanel 
            :title="$t('login')"
            :text="$t('login_desc')" 
            :route="'login'"
            :color="'info'" 
            />

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

  metaInfo () {
    return { title: this.$t('authentificate_title') }
  },

}
</script>

<style lang="scss">

</style>
