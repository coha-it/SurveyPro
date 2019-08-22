<template>
  <div>

    <!-- PAN - User -->
    <template v-if="user && user.pan && user.pan.is_pan_user === 1">
      
    </template>

    <!-- E-Mail User -->
    <template v-else>
      <!-- Alert -->
      <alert-success :form="form" :message="$t('info_updated')" />
      <alert-error :form="form" :message="$t('error_alert_text')" />

      <h1>{{ $t('profile.h1') }}</h1>
      <p>{{ $t('your_info') }}</p>

      <!-- Form -->
      <v-form @submit.prevent="update" @keydown="form.onKeydown($event)" style="    max-width: 350px;">
        <v-container>
          <v-row>
            <v-col cols="12" sm="12" md="12" class="pa-0">
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
            <v-col cols="12" sm="12" md="12" class="pa-0" align="right">
              <!-- Submit Button -->
              <v-btn color="accent" large block :loading="form.busy" type="submit">{{ $t('update') }}</v-btn>
            </v-col>
          </v-row>

        </v-container>
      </v-form>
    </template>

  </div>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('profile.title') }
  },

  data: () => ({
    form: new Form({
      name: '',
      email: ''
    })
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })
  },

  methods: {
    async update () {
      const { data } = await this.form.patch('/api/settings/profile')

      this.$store.dispatch('auth/updateUser', { user: data })
    }
  }
}
</script>
