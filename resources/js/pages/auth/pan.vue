<template>
    <div class="rightsided-content coha--pan-wrapper">


        <template>
          <div class="text-center">
            <v-snackbar
              v-model="snackbar"
              :multi-line="true"
              right 
              color="error"
            >
              {{ $t(snackbarText) }}
              <v-btn
                color="black"
                text
                @click="snackbar = false"
              >
                {{ $t('closer_button') }}
              </v-btn>
            </v-snackbar>
          </div>
        </template>


        <Back />

        <v-form @submit.prevent="loginpan" @keydown="form.onKeydown($event)">
            <v-text-field v-model="form.pan" :label="$t('PAN')" color='black' :error="form.errors.has('pan')" type="text" name="pan" required></v-text-field><br>
            <v-text-field v-model="form.pin" :label="$t('PIN')" color='black' :error="form.errors.has('pin')" type="password" name="pin" required></v-text-field><br>
            <v-btn color="accent" large block :loading="form.busy" type="submit">{{ $t('login') }}</v-btn>
        </v-form>
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

  data: () => ({
    form: new Form({
      pan: '',
      pin: ''
    }),
    snackbar: false,
    snackbarText: '',
  }),

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
    a {
        color: #000;
    }
}
</style>