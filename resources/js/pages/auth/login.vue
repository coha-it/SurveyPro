<template>
  <div class="rightsided-content coha--login-wrapper">
    <div style="max-width: 750px;">
      <v-container>
        <v-row>  
          <v-col cols="12" sm="12" md="12">
            <Back />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" sm="7" md="7" align-self="center">
            <h1>{{ $t('login') }}</h1>
            <p class="subtitle">{{ $t('login_desc') }}</p>
          </v-col>
          <v-col cols="12" sm="5" md="5" align-self="center">
            <v-icon x-large color="white">mdi-account</v-icon>
          </v-col>
        </v-row>
      </v-container>

      <v-form @submit.prevent="login" @keydown="form.onKeydown($event)">
        <v-container>
          <v-row>
            <v-col cols="12" sm="12" md="12">
              <!-- Email -->
              <v-text-field v-model="form.email" :label="$t('E-MAIL')" color='black' :error="form.errors.has('email')" type="email" name="email" required></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="12" md="12">
              <!-- Password -->
              <v-text-field 
              v-model="form.password" 
              :label="$t('PASSWORD')" 
              color='black' 
              :error="form.errors.has('password')"  
              name="password" 
              :type="show1 ? 'text' : 'password'"
              :append-icon="show1 ? 'visibility' : 'visibility_off'"
              @click:append="show1 = !show1"
              required
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="12" md="12" style="padding-bottom: 0;">
              <!-- Remember Me -->
              <v-checkbox v-model="remember" :label="$t('remember_me')" name="remember"></v-checkbox>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="12" md="12" align="right">
              <!-- Submit Button -->
              <v-btn color="accent" large block :loading="form.busy" type="submit">{{ $t('login') }}</v-btn>
            </v-col>
            <v-col cols="12" sm="12" md="12" align="center">

              <p>Neu hier? <router-link :to="{ name: 'password.request' }" class="small ml-auto my-auto">{{ $t('free_register') }}</router-link></p>
              <p>Haben Sie Ihr <router-link :to="{ name: 'password.request' }" class="small ml-auto my-auto">{{ $t('forgot_password') }}</router-link></p>

            </v-col>
          </v-row>
        </v-container>
      </v-form>
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
  metaInfo () {
    return { title: this.$t('login') }
  },

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
      // Submit the form.
      const { data } = await this.form.post('/api/login')

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
  color: rgba(0,0,0,.6);
  background-color: #b6b6b6;

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

  .v-form {
    max-width: 450px;
  }
  .v-input--selection-controls {
    margin-top: 0;
  }
}

</style>
