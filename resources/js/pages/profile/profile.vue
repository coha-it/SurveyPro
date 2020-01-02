<template>
  <div>

            <UserDataModal
                sEditText="Firmen bearbeiten"
                sCreateText="Neue Firma erstellen"
                sInputLabel="Firmenname"
                p_sModel="company"
                :p_oModels="user.companies" />
            <UserDataModal
                sEditText="Abteilungen bearbeiten"
                sCreateText="Neue Abteilung Erstellen"
                sInputLabel="Abteilungsname"
                p_sModel="department"
                :p_oModels="user.departments" />
            <UserDataModal
                sEditText="Orte Bearbeiten"
                sCreateText="Neuen Ort erstellen"
                sInputLabel="Ortsname"
                p_sModel="location"
                :p_oModels="user.locations" />
            <UserDataModal
                sEditText="Gruppen Bearbeiten"
                sCreateText="Neue Gruppe erstellen"
                sInputLabel="Gruppenname"
                sInputLabel2="Gruppenbeschreibung Öffentlich"
                sInputLabel3="Gruppenbeschreibung für Moderatoren"
                p_sModel="group"
                :p_oModels="user.groups_moderating"
                :p_aHeaders="[
                    {
                      name: 'id',
                      label: this.$t('id'),
                      field: 'id',
                    },
                    {
                      name: 'name',
                      label: this.$t('name'),
                      field: 'name',
                    },
                    {
                      name: 'description_public',
                      label: this.$t('description_public'),
                      field: 'description_public',
                    },
                    {
                      name: 'description_mods',
                      label: this.$t('description_mods'),
                      field: 'description_mods',
                    },
                    {
                      name: 'updated_at',
                      label: this.$t('updated_at'),
                      field: 'updated_at',
                    },
                    {
                      name: 'created_at',
                      label: this.$t('created_at'),
                      field: 'created_at',
                    }
                ]" />



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
      <q-form @submit.prevent="update" style="max-width: 550px;" @keydown="form.onKeydown($event)">
        <div class="container">
          <div class="row">
            <v-col cols="12" sm="12" md="12" class="pa-0">
              <q-checkbox
                v-model="form.newsletter"
                :label="$t('newsletter.newsletter')"
              />

              <!-- Email -->
              <q-input
                ref="email"
                v-model="form.email"
                :label="$t('email_label')"
                color="black"
                :error="form.errors.has('email')"
                type="email"
                name="email"
                required
                style="max-width: 350px;"
              />
            </v-col>
          </div>

          <div class="row">
            <v-col cols="12" sm="12" md="12" class="pa-0">
              <!-- Submit Button -->
              <q-btn color="primary" large block :loading="form.busy" type="submit">{{ $t('update') }}</q-btn>
            </v-col>
          </div>

          <br><br><br>

          <div class="row" align="center" style="max-width: 350px;">
            <div class="pa-0 col col-12 col-sm-12 col-md-12">
              <!-- Companies -->
              <br>
              <h2>Firma</h2>
              <p>Passen Sie ihre Firma an</p>

              <br>

              <!-- Select -->
              <select v-model="user.company_id" @change="profileDataChanged('company', user.company_id, user.companies)">
                <option disabled>Bitte auswählen</option>
                <option
                  v-for="company in user.companies"
                  v-bind:key="company.id"
                  :value="company.id"
                  :selected="(user.company && user.company.id == company.id)"
                  >{{ company.name }}</option>
              </select>

              <select v-model="user.location_id" @change="profileDataChanged('location', user.location_id, user.locations)">
                <option disabled>Bitte auswählen</option>
                <option
                  v-for="location in user.locations"
                  v-bind:key="location.id"
                  :value="location.id"
                  :selected="(user.location && user.location.id == location.id)"
                  >{{ location.name }}</option>
              </select>

              <select v-model="user.department_id" @change="profileDataChanged('department', user.department_id, user.departments)">
                <option disabled>Bitte auswählen</option>
                <option
                  v-for="department in user.departments"
                  v-bind:key="department.id"
                  :value="department.id"
                  :selected="(user.department && user.department.id == department.id)"
                  >{{ department.name }}</option>
              </select>

              <!-- Companies Combobox -->
              <Combobox
                :p_oModel="company"
                 p_sModel="company"
                 p_sModels="companies"
                :p_oUser="user"
                p_sInputLabel="profile.company.choose"
                create_text="profile.company.create"
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
            </div>
          </div>
        </div>
      </q-form>
    </template>
  </div>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'
import UserDataModal from '~/components/BackendUserDataModal'

export default {
  scrollToTop: false,

  components: {
    UserDataModal
  },

  data: () => ({
    form: new Form({
      name: '',
      email: '',
      company_id: '',
      department_id: '',
      location_id: ''
    }),
    company: [],
    department: [],
    location: []
  }),

  computed: mapGetters({
    user: 'auth/user'
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
    },
    findById (arr, id) {
      return arr.find(x => x.id === id);
    },
    profileDataChanged (str, id, arr) {
      var f = this.findById(arr, id)
      this.form[str + '_id'] = f.id
    }
  }
}
</script>
