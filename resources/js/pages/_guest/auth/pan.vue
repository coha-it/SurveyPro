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
            <p class="subtitle">
              {{ $t('pan_desc') }}
            </p>
          </div>
          <div class="col-2 col-sm-2 col-md-2 self-center">
            <q-icon name="mdi-dialpad" size="xl" />
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <q-form @submit.prevent="loginpan" @keydown="form.onKeydown($event)">
              <q-input
                ref="pan"
                v-model="form.pan"
                v-mask="'XXX XXX'"
                :standout="'text-white bg-primary'"
                :label="$t('PAN')"
                color="black"
                type="text"
                name="pan"
                persistent-hint
                input-class="big-input"
                :maxlength="pan_maxlength"
                autocomplete="off"
                required
                :error="form.errors.has('pan')"
                @input="changePan"
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
                type="password"
                pattern="[0-9]*"
                name="pin"
                required
                autocomplete="off"
                :maxlength="pin_maxlength"
                input-class="big-input secured"
                persistent-hint
                inputmode="numeric"
                @input="changePin"
              >
                <template v-slot:hint>
                  {{ form.pin.length + ' / ' + (pin_maxlength) }}
                </template>
              </q-input><br>
              <q-btn color="primary" large block :loading="form.busy" type="submit">
                {{ $t('login') }}
              </q-btn>
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
import { mask } from 'vue-the-mask'
import axios from 'axios'

export default {
  middleware: 'guest',
  layout: 'RightsidedLayout',
  components: {
    Back
  },

  directives: {
    mask
  },

  data: () => ({
    pan_maxlength: 7,
    pin_maxlength: 4,
    form: new Form({
      pan: '',
      pin: ''
    }),
    snackbar: false,
    snackbarText: ''
  }),

  created: function () {
    // Check if PAN is in URL
    var pathMatch = this.$router.history.current.params.pathMatch
    if (pathMatch) {
      var urlPan = pathMatch.replace('/', '')
      if (urlPan) {
        this.form.pan = urlPan
      }
    }
  },

  mounted () {
    this.focusPan()
    this.changePan()
  },

  methods: {

    getLengthHint (text, max) {
      if (text && max) {
        return text.replace(/\s/g, '').length + ' / ' + (max - 1)
      }
    },

    changePan () {
      this.form.pan = this.form.pan.toUpperCase()
      if (this.panIsFull()) {
        if (this.pinIsFull()) {
          this.loginpan()
        } else {
          this.focusPin()
        }
      }
    },

    changePin () {
      if (this.pinIsFull()) {
        if (this.panIsFull()) {
          this.loginpan()
        } else {
          this.focusPan()
        }
      }
    },

    focusPin () {
      if (this.$refs.pin) {
        this.$refs.pin.focus()
      }
    },

    focusPan () {
      if (this.$refs.pan) {
        this.$refs.pan.focus()
      }
    },

    panIsFull () {
      return this.form.pan.length >= this.pan_maxlength
    },

    pinIsFull () {
      return this.form.pin.length >= this.pin_maxlength
    },

    async loginpan () {
      // Form is busy?
      if (this.form.busy) return

      // Start Loading
      this.$q.loading.show({ message: 'Anmeldung läuft ...' })

      // Submit the form
      const { data } = await this.form.post('/api/loginpan')
        .catch((error) => {
          this.hideLoader()
          this.form.pin = ''
          if (error && error.response && error.response.data) {
            this.$q.notify({
              message: error.response.data.message,
              icon: 'error',
              color: 'error'
            })
          }
        })

      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      await this.$store.dispatch('auth/fetchUser')

      await this.afterLogin()
    },

    afterLogin () {
      axios.get('/api/first-survey-fillable').then((e) => {
        this.hideLoader()

        const survey = e.data
        if (survey && survey.id) {
          this.goToFirstSurvey(survey)
        } else {
          this.goToHome()
        }
      }).catch((e) => {
        this.hideLoader()
      })
    },

    goToFirstSurvey (s) {
      this.$router.push({
        name: 'survey',
        params: {
          id: s.id
        }
      })
    },

    goToHome () {
      this.$router.push({ name: 'home' })
    },

    hideLoader () {
      this.$q.loading.hide()
    }

  }

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
