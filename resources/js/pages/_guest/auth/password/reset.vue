<template>
  <div class="rightsided-content coha--login-wrapper">
    <div class="inner-content">

      <!-- Back and Header -->
      <v-container>
        <v-row>  
          <v-col cols="12" sm="12" md="12">

            <!-- Error: Snackbar -->
            <template>
              <div class="text-center">
                <v-snackbar v-model="snackbar" top :multi-line="true" right color="error">{{ $t(snackbarText) }}<v-btn color="black" text @click="snackbar = false">{{ $t('closer_button') }}</v-btn></v-snackbar>
              </div>
            </template>

            <!-- Alert -->
              <v-alert
                color="blue-grey"
                dark
                icon="mdi-email-mark-as-unread"
                prominent
                dismissible
                v-model="alert"
                v-show="status"
                class="animated tdDropInLeft"
              >
                <div v-html="$t(status)"></div>
              </v-alert>

            <!-- Back -->
            <!-- <Back :to="{ name:'auth' }" /> -->

          </v-col>
        </v-row>
        <v-row>
          <v-col cols="10" sm="10" md="10" align-self="center">
            <h1>{{ $t('pr_title') }}</h1>
            <p class="subtitle">{{ $t('pr_desc') }}</p>
          </v-col>
          <v-col cols="2" sm="2" md="2" align-self="center">
            <v-icon x-large>mdi-lock-reset</v-icon>
          </v-col>
        </v-row>
      </v-container>


      <!-- Form -->
      <v-form @submit.prevent="reset" @keydown="form.onKeydown($event)">
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
              :hint="form.password.length + ' / 8 ' + $t('password_hint')"
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
              <v-btn color="accent" large block :loading="form.busy" type="submit">{{ $t('reset_password') }}</v-btn>
            </v-col>
          </v-row>

        </v-container>
      </v-form>

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
    status: '',
    form: new Form({
      token: '',
      email: '',
      password: '',
      password_confirmation: '',
    }),

    show1: false,
    alert: false,

    snackbarText: '',
    snackbar: false,
  }),

  created () {
    this.form.email = this.$route.query.email
    this.form.token = this.$route.params.token
  },

  methods: {
    async reset () {
      const { data } = await this.form.post('/api/password/reset')
      .catch((error) => {
        if(error && error.response && error.response.data) {
          this.snackbarText = error.response.data.message || error.response.data.email;
          this.snackbar = false;
          this.snackbar = true;
        }
      });

      this.alert = true
      this.status = data.status
      this.form.reset()
    }
  }
}
</script>
