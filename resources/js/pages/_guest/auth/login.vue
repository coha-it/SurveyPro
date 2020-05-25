<template>
  <div class="rightsided-content coha--login-wrapper">
    <div class="inner-content">
      <!-- Back and Header -->
      <div class="container">
        <div class="row">
          <div clas="cols-12 sm-12 md-12">
            <!-- Back -->
            <Back :to="{ name:'auth' }" />
          </div>
        </div>
        <div class="row">
          <div class="col-10 sm-10 md-10 self-center">
            <h1 class="q-mb-none">
              {{ $t('login_title') }}
            </h1>
            <p class="subtitle">
              {{ $t('login_desc') }}
            </p>
          </div>
          <div class="col-2 sm-2 md-2 self-center">
            <q-icon name="mdi-account" size="xl" color="white" />
          </div>
        </div>
      </div>

      <!-- Form -->
      <q-form @submit="login">
        <div class="container">
          <div class="row">
            <div class="col-12 sm-12 md-12">
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
            <div class="col-12 sm-12 md-12">
              <!-- Password -->
              <q-input
                v-model="form.password"
                filled
                :label="$t('password_label')"
                color="black"
                :error="form.errors.has('password')"
                name="password"
                :type="show1 ? 'text' : 'password'"
                :append-icon="show1 ? 'visibility' : 'visibility_off'"
                required
                @click:append="show1 = !show1"
              />
            </div>
          </div>
          <div class="row">
            <div class="col-12 sm-12 md-12" style="padding-bottom: 0;">
              <!-- Remember Me -->
              <q-checkbox v-model="remember" color="primary" :label="$t('remember_me')" name="remember" />
            </div>
          </div>
          <div class="row">
            <div class="col-12 sm-12 md-12 q-my-md self-right">
              <!-- Submit Button -->
              <q-btn unelevated class="full-width" color="primary" large :loading="form.busy" type="submit">
                {{ $t('login') }}
              </q-btn>
            </div>
            <div class="col-12 sm-12 md-12 self-center text-center">
              <br>
              <p>
                Neu hier? <router-link :to="{ name: 'register' }" class="small ml-auto my-auto">
                  {{ $t('free_register') }}
                </router-link>
              </p>
              <p>
                Haben Sie Ihr <router-link :to="{ name: 'password.request' }" class="small ml-auto my-auto">
                  {{ $t('forgot_password') }}
                </router-link>
              </p>
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
  name: 'Login',
  middleware: 'guest',
  layout: 'RightsidedLayout',
  components: {
    Back
  },
  props: [

  ],

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: false,
    show1: false
  }),

  created: function () {
    // Copy Email from Localstorage if Available
    const lEmail = localStorage.email
    if (lEmail) {
      this.form.email = JSON.parse(JSON.stringify(lEmail))
      localStorage.removeItem('email')
    }
  },

  mounted () {
    this.$refs.email.focus()
  },

  // This was causing Theme-Problems
  // metaInfo () {
  //   return { title: this.$t('login') }
  // },

  methods: {

    async login () {
      // Submit the form.
      const { data } = await this.form.post('/api/login').catch((error) => {
        var response = error.response.data
        this.$q.notify({
          message: this.$t(response.message)
        })
      })

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Redirect home.
      this.$router.push({ name: 'home' })
    }
  }

}
</script>

<style lang="scss" scoped>

.coha--login-wrapper {

  .inner-content {
  }

  h1,
  .subtitle {
    color: #fff;
  }

  .subtitle {
    font-size: 1.15rem;
  }

  a {
    color: #000;
    text-decoration: none;
    font-weight: 600;
  }

  form {
    max-width: 450px;
  }
}

</style>
