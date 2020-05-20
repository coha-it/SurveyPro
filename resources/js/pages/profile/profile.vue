<template>
  <div>
    <!-- Alert -->
    <alert-success :form="form" :message="$t('info_updated')" />
    <alert-error :form="form" :message="$t('error_alert_text') + (response)" />

    <h1>{{ $t('profile.h1') }}</h1>
    <p>{{ $t('your_info') }}</p>

    <q-form style="max-width: 550px;" @submit.prevent="update" @keydown="form.onKeydown($event)">
      <div class="container">
        <!-- E-Mail User -->
        <template v-if="user.is_panuser === false">
          <!-- Form -->

          <div class="row">
            <div class="pa-0 col">

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
                outlined
                style="max-width: 350px;"
              >
                <template v-slot:prepend>
                  <q-icon name="email" />
                </template>
              </q-input>

              <br>

              <q-checkbox
                ref="newsletter"
                v-model="form.newsletter"
                type="checkbox"
                :label="$t('newsletter.newsletter')"
              />
            </div>
          </div>

          <br><br>

          <!-- <div class="row" align="center" style="max-width: 350px;">
            <div class="pa-0 col col-12 col-sm-12 col-md-12">

              <h2>Firma</h2>
              <p>Passen Sie ihre Firma an</p>

              <br>

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
          </div> -->
        </template>

        <!-- PAN - User -->
        <template v-if="user.is_panuser">
          <div class="row">
            <div class="pa-0 col">
              <q-checkbox v-model="bChangePin" label="Pin Ändern" />
              <template v-if="bChangePin">
                <br>
                <br>

                <q-input
                  ref="pin"
                  v-model="form.pin"
                  label="Aktuelle Pin"
                  color="black"
                  :error="form.errors.has('pin')"
                  required
                  style="max-width: 350px;"
                  :type="bShowCurrentPin ? 'text' : 'password'"
                  outlined
                  :placeholder="'Ihre bisherige Pin'"
                  mask="####"
                >
                  <template v-slot:prepend>
                    <q-icon name="lock" />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      name="visibility"
                      class="pointer"
                      @mousedown="bShowCurrentPin = true"
                      @mouseup="bShowCurrentPin = false"
                    />
                  </template>
                </q-input>

                <q-input
                  ref="new_pin"
                  v-model="form.new_pin"
                  label="Neue Pin"
                  color="black"
                  :error="form.errors.has('new_pin')"
                  required
                  style="max-width: 350px;"
                  :type="bShowNewPin ? 'text' : 'password'"
                  outlined
                  :placeholder="'z.B.: ' + randPin()"
                  mask="####"
                  hint="Mask: Vierstellige Nummern-kombination"
                >
                  <template v-slot:prepend>
                    <q-icon name="dialpad" />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      name="visibility"
                      class="pointer"
                      @mousedown="bShowNewPin = true"
                      @mouseup="bShowNewPin = false"
                    />
                  </template>
                </q-input>
              </template>
              <br><br>
            </div>
          </div>
        </template>

        <div class="row">
          <div cols="12" sm="12" md="12" class="pa-0">
            <!-- Submit Button -->
            <q-btn color="primary" large block :loading="form.busy" type="submit">{{ $t('update') }}</q-btn>
          </div>
        </div>
      </div>
    </q-form>
  </div>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'
// import UserDataModal from '~/components/BackendUserDataModal'

export default {
  scrollToTop: false,

  // components: {
  //   UserDataModal
  // },

  data: () => ({

    // Response
    response: '',

    // Hide Show
    bChangePin: false,
    bShowCurrentPin: false,
    bShowNewPin: false,

    // Form
    form: new Form({
      name: '',
      email: '',
      company_id: '',
      department_id: '',
      location_id: '',
      newsletter: false,
      pin: '',
      new_pin: ''
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

    this.form.newsletter = this.user.newsletter ? true : false
  },

  methods: {
    randPin () {
      let max = 9999
      let min = 1000

      return Math.floor(Math.random() * (max - min + 1) + min)
    },
    async update () {
      const { data } = await this.form.patch('/api/settings/profile')
      this.$store.dispatch('auth/updateUser', { user: data })
        .then((e) => {
          this.form.pin = ''
          this.form.new_pin = ''
        })
    },
    findById (arr, id) {
      return arr.find(x => x.id === id)
    },
    profileDataChanged (str, id, arr) {
      var f = this.findById(arr, id)
      this.form[str + '_id'] = f.id
    }
  }
}
</script>
