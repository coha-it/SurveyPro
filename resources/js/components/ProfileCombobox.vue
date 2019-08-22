<template>
    <div>
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

        <v-btn @click="getCompanies">Firmen Aktualisieren</v-btn>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'

export default {

    data: () => ({
        all_companies: [],
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

    computed: mapGetters({
        user: 'auth/user',
    }),

    watch: {
        company (val, prev) {
            this.watchProfileInput(val, prev);
        },
    },

    created() {
        if(!this.all_companies || !this.all_companies.length) {
        this.getCompanies();
        }

        this.company[0] = this.user.company;
    },

    methods: {
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

    },
}
</script>