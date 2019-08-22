<template>
  <div class="rightsided-content coha--register-wrapper">
    <div class="inner-content">

      <!-- Back and Header -->
      <v-container>
        <v-row>  
          <v-col cols="12" sm="12" md="12">
            <Back :to="{ name:'auth' }" />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="10" sm="10" md="10" align-self="center">
            <h1>{{ $t('register_title') }}</h1>
            <p class="subtitle">{{ $t('register_desc') }}</p>
          </v-col>
          <v-col cols="2" sm="2" md="2" align-self="center">
            <v-icon x-large color="white">mdi-account</v-icon>
          </v-col>
        </v-row>
      </v-container>


      <!-- Form -->
      <template v-if="mustVerifyEmail">
        {{ $t('verify_email_address') }}
      </template>

      <template v-else>
        <v-form @submit.prevent="register" @keydown="form.onKeydown($event)">
          <v-container>
            <v-row>
              <v-col cols="12" sm="12" md="12">
                <!-- Email -->
                <v-text-field 
                  v-model="form.email" 
                  :label="$t('email_label')" 
                  color='black' 
                  :error="form.errors.has('email')" 
                  type="email" 
                  name="email" 
                  required
                  ref="email"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" sm="12" md="12">
                <!-- Password -->
                <v-text-field 
                v-model="form.password" 
                :label="$t('password_label')" 
                :error="form.errors.has('password')"  
                name="password" 
                :type="show1 ? 'text' : 'password'"
                :append-icon="show1 ? 'visibility' : 'visibility_off'"
                @click:append="show1 = !show1"
                required
                color="black"
                :hint="form.password.length + ' / 6 ' + $t('password_hint')"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="12" md="12">
                <!-- Password Repeat -->
                <v-text-field 
                v-model="form.password_confirmation" 
                :label="$t('password_label')" 
                color='black' 
                :error="form.errors.has('password_confirmation')"  
                name="password_confirmation" 
                :type="show1 ? 'text' : 'password'"
                :append-icon="show1 ? 'visibility' : 'visibility_off'"
                @click:append="show1 = !show1"
                required 
                :hint="$t('password_repeat_hint')"

                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="12" md="12" align="right">
                <!-- Submit Button -->
                <v-btn color="primary" large block :loading="form.busy" type="submit">{{ $t('register') }}</v-btn>
              </v-col>
            </v-row>
          
          </v-container>

        </v-form>
        <!-- End Form -->

      </template>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import Back from '~/components/AuthBack.vue'

export default {
  middleware: 'guest',
  layout: 'rightsided',
  components: {
    Back
  },

  metaInfo () {
    return { title: this.$t('register') }
  },

  data: () => ({
    form: new Form({
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    }),
    show1: false,
    mustVerifyEmail: false
  }),

  mounted(){
    this.$refs.email.focus();
  },

  methods: {
    async register () {
      // Register the user.
      const { data } = await this.form.post('/api/register')

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true
      } else {
        // Log in the user.
        const { data: { token } } = await this.form.post('/api/login')

        // Save the token.
        this.$store.dispatch('auth/saveToken', { token })

        // Update the user.
        await this.$store.dispatch('auth/updateUser', { user: data })

        // Redirect home.
        this.$router.push({ name: 'home' })
      }
    },

    passwordIsNotCorrect() {
      let a = this.form.password;
      let b = this.form.password_confirmation;


      if(a === '' || b === '') {
        return false;
      }

      if(a === b) {
        return false;
      }

      return true;
    },

  }
}
</script>
