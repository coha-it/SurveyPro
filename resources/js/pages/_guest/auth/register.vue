<template>
  <div class="rightsided-content coha--register-wrapper">
    <div class="inner-content">

      <!-- Back and Header -->
      <div class="container">
        <div class="row">
          <div class="col col-12 col-sm-12 col-md-12">
            <Back :to="{ name:'auth' }" />
          </div>
        </div>
        <div class="row">
          <div class="col col-10 col-sm-12 col-md-12 align-center">
            <h1>{{ $t('register_title') }}</h1>
            <p class="subtitle">{{ $t('register_desc') }}</p>
          </div>
          <div class="col col-2 col-sm-12 col-md-12 align-center">
            <q-icon name="account" x-large color="white" />
          </div>
        </div>
      </div>


      <!-- Form -->
      <template v-if="mustVerifyEmail">
        {{ $t('verify_email_address') }}
      </template>

      <template v-else>
        <q-form @submit.prevent="register" @keydown="form.onKeydown($event)">
          <div class="container">
            <div class="row">
              <div class="col col-12 col-sm-12 md-12">
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
              </div>
            </div>
            <div class="row">
              <div class="col col-12 col-sm-12 md-12">
                <!-- Password -->
                <q-input 
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
                />
              </div>
            </div>

            <div class="row">
              <div class="col col-12 col-sm-12 md-12">
                <!-- Password Repeat -->
                <q-input 
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

                />
              </div>
            </div>

            <div class="row">
              <div class="col col-12 col-sm-12 md-12 align-right">
                <!-- Submit Button -->
                <q-btn color="primary" large block :loading="form.busy" type="submit" :label="$t('register')" />
              </div>
            </div>
          
          </div>

        </q-form>
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

  // This was causing Theme-Problems
  // metaInfo () {
  //   return { title: this.$t('register') }
  // },

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
