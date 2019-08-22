<template>
    <div class="rightsided-content coha--pan-wrapper">

      <template>
        <div class="text-center">
          <v-snackbar v-model="snackbar" top :multi-line="true" right color="error">{{ $t(snackbarText) }}<v-btn color="black" text @click="snackbar = false">{{ $t('closer_button') }}</v-btn></v-snackbar>
        </div>
      </template>

      <div class="inner-content">
          <v-container>
            <v-row>  
              <v-col cols="12" sm="12" md="12">
                <Back :to="{ name:'auth' }" />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="10" sm="10" md="10" align-self="center">
                <h1>{{ $t('pan_title') }}</h1>
                <p class="subtitle">{{ $t('pan_desc') }}</p>
              </v-col>
              <v-col cols="2" sm="2" md="2" align-self="center">
                <v-icon x-large color="white">mdi-account</v-icon>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-form @submit.prevent="loginpan" @keydown="form.onKeydown($event)">
                    <v-text-field 
                    v-model="form.pan" 
                    :label="$t('PAN')" 
                    color='black' 
                    :error="form.errors.has('pan')" 
                    type="text" 
                    name="pan" 
                    required 
                    ref="pan" 
                    counter 
                    maxlength="6" 
                    autocomplete="newpassword" 

                    ></v-text-field><br>
                    <v-text-field v-model="form.pin" :label="$t('PIN')" color='black' :error="form.errors.has('pin')" type="password" name="pin" required autocomplete="newpassword"></v-text-field><br>
                    <v-btn color="primary" large block :loading="form.busy" type="submit">{{ $t('login') }}</v-btn>
                </v-form>
              </v-col>
            </v-row>
          </v-container>
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

  directives: {
  },

  data: () => ({
    form: new Form({
      pan: '',
      pin: ''
    }),
    snackbar: false,
    snackbarText: '',
  }),

  // created: function() {},

  mounted(){
    this.$refs.pan.focus();
  },

  methods: {

    async loginpan () {

      this.snackbar = false;

      // Submit the form.
      const { data } = await this.form.post('/api/loginpan')
      .catch((error) => {
        if(error && error.response && error.response.data) {
          this.snackbarText = error.response.data.message;
          this.snackbar = false;
          this.snackbar = true;
        }
      });

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
.coha--pan-wrapper {
    background-color: #E8D03E;
    color: #434343;
    a {
        color: #000;
    }
}
</style>