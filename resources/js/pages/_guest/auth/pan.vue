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
                    :hint="getLengthHint(form.pan, pan_maxlength)"
                    :maxlength="pan_maxlength" 
                    autocomplete="off" 
                    v-mask="'XXX XXX'" 
                    v-on:input="changePan" 
                    persistent-hint 
                    ></v-text-field><br>

                    <v-text-field 
                    v-model="form.pin" 
                    :label="$t('PIN')" 
                    color='black' 
                    :error="form.errors.has('pin')" 
                    type="text" 
                    pattern="[0-9]*"
                    name="pin" 
                    ref="pin"
                    required 
                    autocomplete="off" 
                    :maxlength="pin_maxlength" 
                    :hint="form.pin.length + ' / ' + (pin_maxlength)" 
                    v-mask="'####'" 
                    v-on:input="changePin" 
                    class="pin" 
                    persistent-hint 
                    ></v-text-field><br>
                    <v-btn color="primary" large block :loading="form.busy" type="submit">{{ $t('login') }}</v-btn>
                    <v-btn color="secondary">secondary</v-btn>
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
import {mask} from 'vue-the-mask'

export default {
  middleware: 'guest',
  layout: 'rightsided',
  components: {
      Back
  },

  directives: {
    mask,
  },

  data: () => ({
    pan_maxlength: 7,
    pin_maxlength: 4,
    form: new Form({
      pan: '',
      pin: ''
    }),
    snackbar: false,
    snackbarText: '',
  }),

  created: function() {
    // Check if PAN is in URL
    var pathMatch = this.$router.history.current.params.pathMatch;
    if(pathMatch)
    {
      var urlPan = pathMatch.replace("/", "");
      if(urlPan)
      {
        this.form.pan = urlPan;
      }
    }
  },

  mounted(){
    this.focusPan();
    this.changePan();
  },

  methods: {

    getLengthHint(text, max) {
      if(text && max) {
        return text.replace(/\s/g, '').length + ' / ' + (max - 1);
      }
    },

    changePan() {
      this.form.pan = this.form.pan.toUpperCase();
      if(this.panIsFull()) {
        if(this.pinIsFull()) {
          this.loginpan();
        } else {
          this.focusPin();
        }
      }
    },

    changePin() {
      if(this.pinIsFull()) {
        if(this.panIsFull()) {
          this.loginpan();
        } else {
          this.focusPan();
        }
      }
    },

    focusPin() {
      if(this.$refs.pin) {
        this.$refs.pin.focus();
      }
    },

    focusPan() {
      if(this.$refs.pan) {
        this.$refs.pan.focus();
      }
    },

    panIsFull() {
      return this.form.pan.length >= this.pan_maxlength;
    },

    pinIsFull() {
      return this.form.pin.length >= this.pin_maxlength;
    },


    async loginpan () {

      var _this = this;

      if(this.form.busy) return;

      this.snackbar = false;

      // Submit the form.
      const { data } = await this.form.post('/api/loginpan')
      .catch((error) => {
        _this.form.pin = '';
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
    },

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