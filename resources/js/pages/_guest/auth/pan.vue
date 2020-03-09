<template>
  <div class="rightsided-content coha--pan-wrapper">
    <div class="inner-content">
      <div class="container">
        <div class="row items-center content-center">
          <div class="col-12 col-sm-12 col-md12">
            <Back :to="{ name:'auth' }" />
          </div>
        </div>
        <div class="row">
          <div class="col-10 col-sm-10 col-md-10 self-center">
            <h1>{{ $t('pan_title') }}</h1>
            <p class="subtitle">{{ $t('pan_desc') }}</p>
          </div>
          <div class="col-2 col-sm-2 col-md-2 self-center">
            <q-icon name="mdi-dialpad" size="xl"></q-icon>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <q-form @submit.prevent="loginpan" @keydown="form.onKeydown($event)">
              <q-input
                v-model="form.pan"
                :standout="'text-white bg-primary'"
                :label="$t('PAN')"
                color='black'
                XXXerror="form.errors.has('pan')"
                type="text"
                name="pan"
                required
                ref="pan"
                :maxlength="pan_maxlength"
                autocomplete="off"
                v-mask="'XXX XXX'"
                v-on:input="changePan"
                persistent-hint
                class="big-input"
              >
                <template v-slot:hint>
                  {{ getLengthHint(form.pan, pan_maxlength) }}
                </template>
              </q-input>

              <br>

              <q-input
                ref="pin"
                v-model="form.pin"
                v-mask="'####'"
                :label="$t('PIN')"
                color="black"
                :standout="'text-white bg-primary'"
                :error="form.errors.has('pin') ? true : false"
                type="text"
                pattern="[0-9]*"
                name="pin"
                required
                autocomplete="off"
                :maxlength="pin_maxlength"
                class="big-input secured"
                persistent-hint
                @input="changePin"
              >
                <template v-slot:hint>
                  {{ form.pin.length + ' / ' + (pin_maxlength) }}
                </template>
              </q-input><br>
              <q-btn color="primary" large block :loading="form.busy" type="submit">{{ $t('login') }}</q-btn>
            </q-form>
          </div>
        </div>
      </div>
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

      // Submit the form.
      const { data } = await this.form.post('/api/loginpan')
      .catch((error) => {
        _this.form.pin = '';
        if(error && error.response && error.response.data) {
          this.$q.notify({
            message: error.response.data.message,
            icon: 'error',
            color: 'error',
          });
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
