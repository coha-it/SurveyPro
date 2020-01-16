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
            <p class="subtitle">{{ $t('pr_desc') }}</p>
          </div>
          <v-col class="col-2 col-sm-2 col-md-2" align-self="center">
            <v-icon x-large>mdi-lock-reset</v-icon>
          </v-col>
        </div>
      </div>


      <!-- Form -->
      <q-form @submit.prevent="reset" @keydown="form.onKeydown($event)">
        <div class="container">
          <div class="row">
            <v-col class="col-12 col-sm-12 col-md-12">
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
          </div>

          <div class="row">
            <v-col class="col-12 col-sm-12 col-md-12">
              <!-- Password -->
              <q-input
                v-model="form.password"
                :label="$t('password_label')"
                :error="form.errors.has('password')"
                name="password"
                :type="bShowPw ? 'text' : 'password'"
                :append-icon="bShowPw ? 'visibility' : 'visibility_off'"
                @click:append="bShowPw = !bShowPw"
                required
                color="black"
                :hint="form.password.length + ' / 8 ' + $t('password_hint')"
              />
            </v-col>
          </div>

          <div class="row">
            <v-col class="col-12 col-sm-12 col-md-12">
              <!-- Password Repeat -->
              <q-input
                v-model="form.password_confirmation"
                :label="$t('password_label')"
                color='black'
                :error="form.errors.has('password_confirmation')"
                name="password_confirmation"
                :type="bShowPw ? 'text' : 'password'"
                :append-icon="bShowPw ? 'visibility' : 'visibility_off'"
                @click:append="bShowPw = !bShowPw"
                required
                :hint="$t('password_repeat_hint')"
              />
            </v-col>
          </div>

          <div class="row">
            <v-col class="col-12 col-sm-12 col-md-12" align="right">
              <!-- Submit Button -->
              <q-btn
                :loading="form.busy"
                color="accent"
                large
                block
                type="submit"
                :label="$t('reset_password')"
              />
            </v-col>
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
  layout:'rightsided',

  metaInfo () {
    return { title: this.$t('reset_password') }
  },

  data: () => ({
    form: new Form({
      token: '',
      email: '',
      password: '',
      password_confirmation: '',
    }),

    bShowPw: false,
  }),

  created () {
    this.form.email = this.$route.query.email
    this.form.token = this.$route.params.token
  },
  methods: {
    alertSuccess: function(msg) {
      this.$q.notify({
        message: this.$t(msg),
        position: 'top-right',
        color: 'primary',
        timeout: 3000
      })
    },
    alertError: function(msg) {
      this.$q.notify({
        message: this.$t(msg),
        position: 'top-right',
        color: 'red',
        timeout: 3000
      })
    },
    async reset () {
      const { data } = await this.form.post('/api/password/reset')
      .catch((error) => {
        if(error && error.response && error.response.data) {
          this.alertError(error.response.data.message || error.response.data.email)
        }
      });

      // Success Alert
      this.alertSuccess(data.status)

      this.form.reset()
    }
  }
}
</script>
