<template>
  <div class="rightsided-content coha--login-wrapper">
    <div class="inner-content">

      <!-- Back and Header -->
      <v-container>
        <v-row>  
          <v-col cols="12" sm="12" md="12">

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

              <!-- Error Alert -->
              <v-alert dark icon="mdi-alert" prominent v-if="form.errors.has('email')" dismissible>
                <template v-for="errors in form.errors">
                  <template v-for="(error, i) in errors">
                    <div v-html="$t(error)" v-bind:key="i"></div>
                  </template>
                </template>
              </v-alert>

            <!-- Back -->
            <Back :to="{name: 'login'}" />

          </v-col>
        </v-row>
        <v-row>
          <v-col cols="10" sm="10" md="10" align-self="center">
            <h1>{{ $t('prm_title') }}</h1>
            <p class="subtitle">{{ $t('prm_desc') }}</p>
          </v-col>
          <v-col cols="2" sm="2" md="2" align-self="center">
            <v-icon x-large >email</v-icon>
          </v-col>
        </v-row>
      </v-container>

      <!-- Form -->
      <v-form @submit.prevent="send" @keydown="form.onKeydown($event)">
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
            <v-col cols="12" sm="12" md="12" align="right">
              <!-- Submit Button -->
              <v-btn color="primary" large block :loading="form.busy" type="submit">{{ $t('send_password_reset_link') }}</v-btn>
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
  middleware: 'guest',
  layout: 'rightsided',

  components: {
    Back
  },

  metaInfo () {
    return { title: this.$t('reset_password') }
  },

  data: () => ({
    status: '',
    form: new Form({
      email: ''
    }),
    alert: true,
  }),

  methods: {
    async send () {
      const { data } = await this.form.post('/api/password/email')

      this.alert = true;
      this.status = data.status
      this.form.reset()
    }
  }
}
</script>
