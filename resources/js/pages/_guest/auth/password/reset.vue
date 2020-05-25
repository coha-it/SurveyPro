<template>
  <div class="rightsided-content coha--login-wrapper">
    <div class="inner-content">
      <!-- Back and Header -->
      <div class="container">
        <div class="row">
          <div class="col-12 col-sm-12 col-md-12">
            <!-- Back -->
            <!-- <Back :to="{ name:'auth' }" /> -->
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-sm-10 col-md-10" align-self="center">
            <h1>{{ $t('pr_title') }}</h1>
            <p class="subtitle">
              {{ $t('pr_desc') }}
            </p>
          </div>
          <div class="col-2 col-sm-2 col-md-2" align-self="center">
            <q-icon name="mdi-lock-reset" x-large />
          </div>
        </div>
      </div>

      <!-- Form -->
      <q-form @submit.prevent="reset" @keydown="form.onKeydown($event)">
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
                readonly
                disable
                required
              />
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-sm-12 col-md-12">
              <!-- Password -->
              <q-input
                v-model="form.password"
                filled
                :label="$t('password_label')"
                :error="form.errors.has('password')"
                name="password"
                :type="bShowPw ? 'text' : 'password'"
                :append-icon="bShowPw ? 'visibility' : 'visibility_off'"
                required
                color="black"
                :hint="getPasswordHint()"
                autofocus
                @click:append="bShowPw = !bShowPw"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-sm-12 col-md-12">
              <!-- Password Repeat -->
              <q-input
                v-model="form.password_confirmation"
                filled
                :label="$t('password_repeat_hint')"
                color="black"
                :error="form.errors.has('password_confirmation')"
                name="password_confirmation"
                :type="bShowPw ? 'text' : 'password'"
                :append-icon="bShowPw ? 'visibility' : 'visibility_off'"
                required
                :hint="getPasswordConfirmHint()"
                @click:append="bShowPw = !bShowPw"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-sm-12 col-md-12" align="right">
              <!-- Submit Button -->
              <q-btn
                :loading="form.busy"
                color="primary"
                large
                unelevated
                class="full-width"
                block
                type="submit"
                :label="$t('reset_password')"
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

export default {
  middleware: 'guest',
  layout: 'RightsidedLayout',

  metaInfo () {
    return { title: this.$t('reset_password') }
  },

  data: () => ({
    form: new Form({
      token: '',
      email: '',
      password: '',
      password_confirmation: ''
    }),

    bShowPw: false
  }),

  created () {
    this.form.email = this.$route.query.email
    this.form.token = this.$route.params.token
  },
  methods: {
    alertSuccess: function (msg) {
      this.$q.notify({
        message: 'Erfolgreich zurückgesetzt',
        caption: this.$t(msg),
        position: 'top-right',
        color: 'primary',
        timeout: 6000
      })
    },
    alertError: function (msg) {
      this.$q.notify({
        message: 'Error!',
        caption: this.$t(msg),
        position: 'top-right',
        color: 'red',
        timeout: 6000
      })
    },
    async reset () {
      const { data } = await this.form.post('/api/password/reset')
        .catch((error) => {
          if (error && error.response && error.response.data) {
            this.alertError(error.response.data.message || error.response.data.email)
          }
        })

      // Success Alert
      this.alertSuccess(data.status)

      // Push to Login
      const tmpMail = JSON.parse(JSON.stringify(this.form.email))
      setTimeout(() => {
        localStorage.email = tmpMail // Set Localstorage
        this.$router.push({ name: 'login' })
      }, 800)

      this.form.reset()
    },
    getPasswordHint () {
      const pw = this.form.password

      if (pw) {
        return this.form.password.length + ' / 8 ' + this.$t('password_hint')
      }
      return null
    },
    getPasswordConfirmHint () {
      const pw = this.form.password
      const pwc = this.form.password_confirmation

      if (pwc) {
        if (pw === pwc) {
          return 'Passwörter Gleich'
        }
        return 'Passwörter nicht gleich'
      }

      return null
    }
  }
}
</script>
