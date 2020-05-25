<template>
  <div class="rightsided-content">
    <div class="inner-content">
      <h1>Willkommen</h1>
      <p style="max-width: 400px;">
        Um sich in ihr SurveyPro-Konto anzumelden, klicken Sie auf eine der drei unterstehenden Optionen
      </p>
      <div style="max-width: 500px;">
        <q-item-label header class="divider"><span>{{ $t('pan_and_pin') }}</span></q-item-label>
        <AuthPanel
          :title="$t('pan_title')"
          :text="$t('pan_desc')"
          :route="'pan'"
          :color="'accent'"
          :icon="'mdi-dialpad'"
        />

        <q-item-label header class="divider"><span>{{ $t('with_email') }}</span></q-item-label>
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
import AuthPanel from '~/components/AuthPanel'

export default {

  middleware: 'guest',
  layout: 'RightsidedLayout',

  components: {
    AuthPanel
  }

}
</script>
