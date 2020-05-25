<template>
  <div class="rightsided-content coha--login-wrapper">
    <div class="inner-content">
      <!-- Back and Header -->
      <v-container>
        <v-row>
          <v-col cols="12" sm="12" md="12">
            <!-- Alert -->
            <alert-success :form="form" :message="status" />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="10" sm="10" md="10" align-self="center">
            <h1>{{ $t('verify_email') }}</h1>
            <p class="subtitle">{{ $t('verify_email_desc') }}</p>
          </v-col>
        </v-row>

        <form @submit.prevent="send" @keydown="form.onKeydown($event)">
          <v-row>
            <v-col>

                <!-- Email -->
                <q-input
                  v-model="form.email"
                  :label="$t('email_label')"
                  color='black'
                  :error="form.errors.has('email')"
                  type="email"
                  name="email"
                  required
                  ref="email"
                />
            </v-col>
          </v-row>
          <v-row>
              <v-col cols="12" sm="12" md="12" align="right">
                <!-- Submit Button -->
                <q-btn color="primary" large block :loading="form.busy" type="submit" :label="$t('send_verification_link')" />
              </v-col>
          </v-row>
        </form>
      </v-container>
    </div>
  </div>
</template>

<script>
import Form from 'vform'

export default {
  middleware: 'guest',
  layout: 'RightsidedLayout',

  data: () => ({
    status: '',
    form: new Form({
      email: ''
    })
  }),

  created () {
    if (this.$route.query.email) {
      this.form.email = this.$route.query.email
    }
  },

  methods: {
    async send () {
      const { data } = await this.form.post('/api/email/resend')

      this.status = data.status

      this.form.reset()
    }
  }
}
</script>
