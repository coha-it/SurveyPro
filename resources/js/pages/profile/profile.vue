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
              <template>
                <v-combobox
                  v-model="company"
                  :filter="filter"
                  :hide-no-data="!search"
                  :items="all_companies"
                  :item-text="'name'"
                  :item-value="'id'"
                  :search-input.sync="search"
                  label="Search for an option"
                  multiple
                  dense 
                  solo 
                  autocomplete="off"
                >
                  <template v-slot:no-data>
                    <v-list-item>
                      <span class="subheading">{{ $t('profile.company.create') }}</span>&nbsp; "{{ search }}"
                    </v-list-item>
                  </template>
                  <template v-slot:selection="{ attrs, item, parent, selected }">
                    <template v-if="item === Object(item)" v-bind="attrs" :input-value="selected">
                      <span style="padding-right: 3px;">
                        {{ item.name }}
                      </span>
                      <v-icon
                        small
                        @click="parent.selectItem(item)"
                      >close</v-icon>
                    </template>
                  </template>
                  <template v-slot:item="{ index, item }">
                    <v-text-field
                      v-if="editing === item"
                      v-model="editing.name"
                      autofocus
                      flat
                      background-color="transparent"
                      hide-details
                      solo
                      @keyup.enter="edit(index, item)"
                    ></v-text-field>
                    <template v-else small>
                      {{ item.name }}
                    </template>
                    <div class="flex-grow-1"></div>
                    <v-list-item-action @click.stop>
                      <v-btn
                        icon
                        @click.stop.prevent="edit(index, item)"
                      >
                        <v-icon>{{ editing !== item ? 'edit' : 'check' }}</v-icon>
                      </v-btn>
                    </v-list-item-action>
                  </template>
                </v-combobox>
              </template>

              <v-btn @click="getCompanies">Firmen Aktualisieren</v-btn>

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
import axios from 'axios'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('profile.title') }
  },

  data: () => ({
    all_companies: [],
    form: new Form({
      name: '',
      email: ''
    }),

    activator: null,
    attach: null,
    editing: null,
    index: -1,
    nonce: 1,
    menu: false,
    company: [],
    x: 0,
    search: null,
    y: 0,
  }),

  watch: {
    company (val, prev) {
      this.watchProfileInput(val, prev);
    },
  },

  computed: mapGetters({
    user: 'auth/user',
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })

    if(!this.all_companies || !this.all_companies.length) {
      this.getCompanies();
    }

    this.company[0] = this.user.company;
  },

  methods: {
    async update () {
      const { data } = await this.form.patch('/api/settings/profile')

      this.$store.dispatch('auth/updateUser', { user: data })
    },

    getCompanies() {
      var _this = this;
      // Make a request for a user with a given ID
      axios.get('/api/companies/all')
        .then(function (response) {
          // handle success
          _this.all_companies = response.data;
        })
        .catch(function (error) {
          // handle error
          console.log(error);
        })
        .finally(function () {
          // always executed
        });
    },

    edit (index, item) {
      if (!this.editing) {
        this.editing = item
        this.index = index
      } else {
        this.editing = null
        this.index = -1
        console.log('finished');
        // Update Company
        this.updateCompany(item);
      }
    },

    setCompanyForUser(id) {
      axios.patch('/api/user/company/set', { 
        id: id
      });
    },

    updateCompany(item) {
        axios.patch('/api/company/update', {
          id: item.id,
          name: item.name
        });
    },

    filter (item, queryText, itemText) {
      if (item.header) return false
      const hasValue = val => val != null ? val : ''
      const text = hasValue(itemText)
      const query = hasValue(queryText)
      return text.toString()
        .toLowerCase()
        .indexOf(query.toString().toLowerCase()) > -1
    },


    watchProfileInput(val, prev) {
      var _this = this;
      
      if (val.length === prev.length) return

      if(this.company && this.company.length && this.company.length >= 1) {
        // Update Company ID
        console.log('Change Company ID');

        var comp = this.company[this.company.length-1];

        if(typeof comp === 'string') {
          // Make a request for a user with a given ID
          axios.post('/api/company/create', {
            name:comp
          }).then(function (response) {
            console.log(response.data);
            _this.setCompanyForUser(response.data);
          });

        } else if (typeof comp === 'object') {
          // Use Existing Company!
          _this.setCompanyForUser(comp.id)
        }
      } else {
        // Empty Company ID from User
        axios.patch('/api/user/company/set', {
          id: null
        });
      }
      
      
      if (val.length > 1) {
        this.$nextTick(() => this.company.shift())
      }
      this.company = val.map(v => {
        if (typeof v === 'string') {
          v = {
            name: v,
          }
          this.all_companies.push(v)
          this.nonce++
        }
        return v
      })
    }

  }
}
</script>
