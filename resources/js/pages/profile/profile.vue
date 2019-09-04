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
      <v-form @submit.prevent="update" @keydown="form.onKeydown($event)" style="max-width: 550px;">
        <v-container>
          <v-row>
            <v-col cols="12" sm="12" md="12" class="pa-0">

              <v-checkbox
                v-model="form.newsletter"
                :label="$t('newsletter.newsletter')"
              ></v-checkbox>

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
                style="max-width: 350px;"
              ></v-text-field>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="12" md="12" class="pa-0">
              <!-- Submit Button -->
              <v-btn color="primary" large block :loading="form.busy" type="submit">{{ $t('update') }}</v-btn>
            </v-col>
          </v-row>

          <br><br><br>

          <v-row align="center" style="max-width: 350px;">
            <v-col cols="12" sm="12" md="12" class="pa-0">




              <!-- Companies -->
              <br>
              <h2>Firma</h2>
              <p>Passen Sie ihre Firma an</p>

              <br>

              <!-- Companies Combobox -->
              <Combobox 
                create_text="profile.company.create"
                p_sInputLabel="profile.company.choose"
                :p_oModel="company"
                 p_sModel="company"
                 p_sModels="companies"
                :p_oUser="user"
                />

              <Combobox 
                create_text="profile.company.create"
                p_sInputLabel="profile.department.choose"
                p_sModel="department"
                p_sModels="departments"
                :p_oModel="department"
                :p_oUser="user"
                />

              <Combobox 
                create_text="profile.location.create"
                p_sInputLabel="profile.location.choose"
                p_sModel="location"
                p_sModels="locations"
                :p_oModel="location"
                :p_oUser="user"
                />

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
import Combobox from '~/components/ProfileCombobox'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('profile.title') }
  },

  components: {
    Combobox,
  },

  data: () => ({
    form: new Form({
      name: '',
      email: ''
    }),
    company: [],
    department: [],
    location: []
  }),

  computed: mapGetters({
    user: 'auth/user',
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })

    this.form.newsletter = this.user.newsletter ? true : false;
  },

  methods: {
    async update () {
      const { data } = await this.form.patch('/api/settings/profile')

      this.$store.dispatch('auth/updateUser', { user: data })
    }

  }
}
</script>
