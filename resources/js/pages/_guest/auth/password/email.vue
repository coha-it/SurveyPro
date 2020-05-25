<template>
  <div class="rightsided-content coha--login-wrapper">
    <div class="inner-content">
      <!-- Back and Header -->
      <div class="container">
        <div class="row">
          <div class="col-12 col-sm-12 col-md-12">
            <!-- Back -->
            <Back :to="{name: 'login'}" />
          </div>
        </div>
        <div class="row">
          <div class="col-10 col-sm-10 col-md-10" align-self="center">
            <h1>{{ $t('prm_title') }}</h1>
            <p class="subtitle">
              {{ $t('prm_desc') }}
            </p>
          </div>
          <div class="col-2 col-sm-2 col-md-2" align-self="center">
            <q-icon name="mdi-email" size="xl" />
          </div>
        </div>
      </div>

      <!-- Form -->
      <q-form @submit.prevent="send" @keydown="form.onKeydown($event)">
        <div class="container">
          <div class="row">
            <div class="col-12 col-sm-12 col-md-12">
              <!-- Email -->
              <q-input
                ref="email"
                v-model="form.email"
                filled
                :label="$t('email_label')"
                color="black"
                :error="form.errors.has('email')"
                type="email"
                name="email"
                required
              />
            </div>
          </div>

          <div class="row">
            <div class="col col-12">
              <!-- Submit Button -->
              <q-btn
                color="primary"
                size="medium"
                class="full-width q-mb-md"
                :loading="form.busy"
                type="submit"
                unelevated
                :label="$t('send_password_reset_link')"
              />
            </div>
          </div>
        </div>
      </q-form>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import Back from '~/components/AuthBack.vue'

export default {
  middleware: 'guest',
  layout: 'RightsidedLayout',

  components: {
    Back
  },

  data: () => ({
    status: '',
    form: new Form({
      email: ''
    })
  }),

  methods: {
    alertError () {
      this.$q.notify({
        message: this.form.errors,
        position: 'top-right',
        color: 'error',
        timeout: 3000
      })
    },
    alertSuccess () {
      this.$q.notify({
        message: this.$t('email sent'),
        caption: this.$t(this.status),
        actions: [{ icon: 'close', color: 'white' }],
        position: 'top-right',
        color: 'primary',
        timeout: 10000
      })
    },
    async send () {
      // Ajax and Set Status
      const { data } = await this.form.post('/api/password/email')
      this.status = data.status

      // Alerts
      if (this.form.errors.has('email')) {
        this.alertError()
      } else {
        this.alertSuccess()
      }

      // Reset Form
      this.form.reset()
    }
  }
}
</script>
