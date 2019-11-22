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
            <h1>{{ $t('login_title') }}</h1>
            <p class="subtitle">{{ $t('login_desc') }}</p>
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
                v-model="form.email"
                :label="$t('email_label')"
                color='black'
                :error="form.errors.has('email')"
                type="email"
                name="email"
                required
                ref="email"
              ></q-input>
            </div>
          </div>
          <div class="row">
            <div class="col-12 sm-12 md-12">
              <!-- Password -->
              <q-input
              v-model="form.password"
              :label="$t('password_label')"
              color='black'
              :error="form.errors.has('password')"
              name="password"
              :type="show1 ? 'text' : 'password'"
              :append-icon="show1 ? 'visibility' : 'visibility_off'"
              @click:append="show1 = !show1"
              required
              ></q-input>
            </div>
          </div>
          <div class="row">
            <div class="col-12 sm-12 md-12" style="padding-bottom: 0;">
              <!-- Remember Me -->
              <q-checkbox color="primary" v-model="remember" :label="$t('remember_me')" name="remember"></q-checkbox>
            </div>
          </div>
          <div class="row">
            <div class="col-12 sm-12 md-12 self-right">
              <!-- Submit Button -->
              <q-btn color="primary" large block :loading="form.busy" type="submit">{{ $t('login') }}</q-btn>
            </div>
            <div class="col-12 sm-12 md-12 self-center">

              <p>Neu hier? <router-link :to="{ name: 'register' }" class="small ml-auto my-auto">{{ $t('free_register') }}</router-link></p>
              <p>Haben Sie Ihr <router-link :to="{ name: 'password.request' }" class="small ml-auto my-auto">{{ $t('forgot_password') }}</router-link></p>

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
  layout: 'rightsided',
  components: {
    Back
  },
  props: [

  ],
  created: function() {

  },
  mounted(){
    this.$refs.email.focus();
  },

  // This was causing Theme-Problems
  // metaInfo () {
  //   return { title: this.$t('login') }
  // },

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: false,
    show1: false,
  }),

  methods: {

    async login () {
      var _t = this;

      // Submit the form.
      const { data } = await this.form.post('/api/login').catch(function(error) {
        var response = error.response.data;
        _t.$q.notify({
          message: _t.$t(response.message),
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
  },

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
