<template>
    <div>
        <h1>Users</h1>
        <p>Create Users with PAN & PIN</p>
        <br>



        <template>
            <v-card>
                <v-card-title>
                    Created Users
                    <div class="flex-grow-1"></div>
                </v-card-title>
                <v-card-text> 
                    <v-row no-gutters align-content="end" align="end" justify="end"> 
                        <v-col cols="12" md="6">
                            
                            
                            <v-dialog v-model="dialogEditCompanies" max-width="900px" persistent scrollable>
                                <template v-slot:activator="{ on }">
                                    <v-btn small color="primary" depressed v-on="on">Firma bearbeiten</v-btn>
                                </template>
                                <v-card>
                                    <v-card-title class="headline grey lighten-2" primary-title>Firma Bearbeiten</v-card-title>
                                    <v-card-text>
                                        <br>
                                        <v-card>
                                            <v-card-title>
                                                <!-- New Company -->
                                                <v-dialog v-model="dialogCreateCompany" persistent max-width="290">
                                                    <template v-slot:activator="{ on }">
                                                        <v-btn color="primary" dark v-on="on">Create Company</v-btn>
                                                    </template>
                                                    <v-card>
                                                        <v-card-title class="headline">Create Company</v-card-title>
                                                        <v-card-text>
                                                            <v-text-field label="Company" required v-model="company_name"></v-text-field>
                                                        </v-card-text>
                                                        <v-card-actions>
                                                            <div class="flex-grow-1"></div>
                                                            <v-btn color="green darken-1" text @click="dialogCreateCompany = false">cancel</v-btn>
                                                            <v-btn color="green darken-1" text @click="createCompany(company_name)">save</v-btn>
                                                        </v-card-actions>
                                                    </v-card>
                                                </v-dialog>


                                                <div class="flex-grow-1"></div>
                                                <v-text-field
                                                    v-model="search"
                                                    append-icon="search"
                                                    label="Search"
                                                    single-line
                                                    hide-details
                                                ></v-text-field>
                                            </v-card-title>
                                            <v-data-table
                                                :headers="company_headers"
                                                :items="user.companies"
                                                :items-per-page="20"
                                                class="elevation-1"
                                                multi-sort>
                                                <template v-slot:item.name="props">
                                                    <v-edit-dialog
                                                        :return-value.sync="props.item.name"
                                                        @save="saveCompany(props.item)"
                                                        @cancel="cancel"
                                                        @open="open"
                                                        @close="close" 
                                                        lazy 
                                                    > {{ props.item.name }}
                                                        <template v-slot:input>
                                                            <v-text-field
                                                                v-model="props.item.name"
                                                                label="Edit"
                                                                single-line
                                                                counter
                                                            ></v-text-field>
                                                        </template>
                                                    </v-edit-dialog>
                                                </template>
                                            </v-data-table>
                                        </v-card>

                                    </v-card-text>
                                    <v-card-actions>
                                        <div class="flex-grow-1"></div>
                                        <v-btn color="primary" text @click="dialogEditCompanies = false">
                                            close
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>


                            <v-btn small color="primary" depressed>Abteilung bearbeiten</v-btn>
                            <v-btn small color="primary" depressed>Ort bearbeiten</v-btn>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details></v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-data-table
                    v-if="usersCreated && usersCreated.length >= 1"
                    :headers="headers"
                    :items="usersCreated"
                    item-key="id"
                    :search="search"
                    show-select 
                    multi-sort 
                    :loading="loading"
                    :loading-text="$t('loading.text')"
                    >

                    <!-- PAN -->
                    <template v-slot:item.pan.pan="{ item }">
                        <!-- <v-btn @click="item.pan.pan='gusman'">Los</v-btn> -->
                        <span class="coha--list-item pan c-code-text">
                            <v-edit-dialog
                                @cancel="cancel"
                                @open="open"
                                @close="close" 
                                @save="save"
                                :return-value.sync="item.pan.pan"
                                lazy 
                                persistent
                                ref="dialog"
                            >
                                <span :class="!panIsOk(item) ? 'red--text' : ''">
                                    <span class="pan--part">{{ item.pan.pan.substring(0,3) }}</span><span class="pan--part">{{ item.pan.pan.substring(3,6) }}</span>
                                </span>
                                <template v-slot:input>
                                    <div class="pan--dialog-input c-code-text">
                                        <v-text-field 
                                            v-model="item.pan.pan"
                                            :rules="[maxPanChars]"
                                            :label="$t('edit')"
                                            single-line
                                            v-on:change="changePan(item)" 
                                            :error="!panIsOk(item)" 
                                            counter
                                        ></v-text-field>
                                    </div>
                                </template>
                            </v-edit-dialog>
                        </span>                       
                    </template>


                    <!-- PIN -->
                    <template v-slot:item.pan.pin="{ item }">
                        <span class="coha--list-item pin c-code-text">
                            <v-edit-dialog
                                @cancel="cancel"
                                @open="open"
                                @close="close" 
                                @save="save"
                                lazy 
                                persistent
                            >
                                <span class="coha--list-item pin">
                                    <template v-if="item.pan.pin">
                                        <span :class="!pinIsOk(item) ? 'red--text' : ''">{{ item.pan.pin }}</span>
                                    </template>
                                    <template v-else>
                                        <span style="text-transform: uppercase;" class="red--text">{{ $t('empty') }}</span>
                                    </template>
                                </span>
                                <template v-slot:input>
                                    <div class="pin--dialog-input c-code-text">
                                        <v-text-field 
                                            v-model="item.pan.pin"
                                            :label="$t('edit')"
                                            single-line
                                            v-on:change="changePin(item)" 
                                            counter
                                            type="text" 
                                            pattern="[0-9]*"
                                            name="pin" 
                                            v-mask="'####'" 
                                            maxlength="4"
                                            :rules="[maxPinChars]"
                                            required
                                            autocomplete="off" 
                                        ></v-text-field>
                                    </div>
                                </template>
                            </v-edit-dialog>
                        </span>
                    </template>

                    <!-- Groups -->
                    <template v-slot:item.groups="{ item }">
                        <template v-if="groupsModerating && groupsModerating.length >= 1">
                            <v-chip 
                                v-for="(group, i) in item.groups" 
                                v-bind:key="i" 
                                outlined small  
                                class="mr-1"
                            >{{ group.name }}</v-chip>
                            <!-- <v-chip>
                                <select>
                                    <option selected>{{ '+ Add' }}</option>
                                    <option 
                                        v-for="group in groupsModerating" 
                                        v-bind:key="group.id" 
                                        v-if="!alreadyInGroups(group, item.groups)"
                                        >{{ group.name }}</option>
                                </select>
                            </v-chip> -->

                            <!-- Gruppen hinzufügen / entfernen -->
                            <template>
                                <v-dialog v-model="item.groupDialog" scrollable max-width="500px">
                                    <template v-slot:activator="{ on }">
                                        <v-chip v-on="on" small :disabled="item.groupDialog">{{ $t('edit') }}</v-chip>
                                    </template>
                                    <v-card>
                                        <v-card-title>Gruppen hinzufügen / entfernen</v-card-title>
                                        <v-divider></v-divider>

                                        <v-card-text style="height: 500px;">
                                            <p>Der Gewählte Nutzer mit der ID "{{ item.id }}" und mit der PAN "{{ item.pan.pan }}"</p>
                                            <v-card outlined>
                                                <v-list subheader two-line flat>
                                                    <v-subheader>Nutzer ist in Gruppen:</v-subheader>
                                                    <template v-for="(group, i) in item.groups">
                                                        <div v-bind:key="group.id">

                                                            <v-list-item>
                                                                <v-list-item-avatar>
                                                                    <v-icon class="white--text primary">mdi-account-multiple</v-icon>
                                                                </v-list-item-avatar>

                                                                <v-list-item-content>
                                                                    <v-list-item-title>{{ group.name }}</v-list-item-title>
                                                                    <v-list-item-subtitle>{{ group.description_public || 'Ohne Gruppenbeschreibung'}}</v-list-item-subtitle>
                                                                </v-list-item-content>

                                                                <v-list-item-action>
                                                                    <v-list-item-action-text>ID #{{ group.id }}</v-list-item-action-text>
                                                                    <v-btn 
                                                                        depressed 
                                                                        rounded 
                                                                        outlined 
                                                                        text 
                                                                        small 
                                                                        color="error"
                                                                        @click="removeCreatedUserFromGroup(item, i)"
                                                                        >- {{ $t('remove') }}</v-btn>
                                                                </v-list-item-action>
                                                            </v-list-item>

                                                            <v-divider v-if="i+1 < item.groups.length" inset></v-divider>
                                                        </div>
                                                    </template>
                                                </v-list>
                                            </v-card>

                                            <br>

                                            <v-card outlined>
                                                <v-list subheader two-line flat>
                                                    <v-subheader>Nutzer für diese Gruppen hinzufügen</v-subheader>
                                                    <template v-for="(group, i) in groupsModerating">
                                                        <div v-bind:key="group.id">

                                                            <v-list-item :disabled="alreadyInGroups(group, item.groups)">
                                                                <v-list-item-avatar>
                                                                    <v-icon>mdi-account-multiple-plus</v-icon>
                                                                </v-list-item-avatar>

                                                                <v-list-item-content>
                                                                    <v-list-item-title>{{ group.name }}</v-list-item-title>
                                                                    <v-list-item-subtitle>{{ group.description_public || 'Ohne Gruppenbeschreibung' }}</v-list-item-subtitle>
                                                                </v-list-item-content>

                                                                <v-list-item-action>
                                                                    <v-list-item-action-text>ID #{{ group.id }}</v-list-item-action-text>
                                                                    <v-btn 
                                                                        v-if="alreadyInGroups(group, item.groups)" 
                                                                        depressed 
                                                                        rounded 
                                                                        outlined 
                                                                        text 
                                                                        small>{{ $t('added')}}</v-btn>
                                                                    <v-btn 
                                                                        v-else 
                                                                        depressed 
                                                                        rounded 
                                                                        outlined 
                                                                        text 
                                                                        small 
                                                                        color="success"
                                                                        @click="addCreatedUserToGroup(item, group)">+ {{ $t('add_to') }}</v-btn>
                                                                </v-list-item-action>
                                                            </v-list-item>

                                                            <v-divider v-if="i+1 < item.groups.length" inset></v-divider>
                                                        </div>
                                                    </template>
                                                </v-list>
                                            </v-card>
                                        </v-card-text>
                                        <v-divider></v-divider>
                                        <v-card-actions>
                                            <v-btn color="blue darken-1" text @click="item.groupDialog = false">{{ $t('closer_button') }}</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                            </template>

                        </template>
                    </template>

                    <!-- Company -->
                    <template v-slot:item.company="{ item }">
                        <select v-model="item.company_id">
                            <option 
                                :value="company.id" 
                                :selected="item.company.id == company.id"
                                v-for="company in user.companies" 
                                v-bind:key="company.id"
                                >{{ company.name }}</option>
                        </select>
                    </template>

                    <!-- Department -->
                    <template v-slot:item.department="{ item }">
                        <select v-model="item.department_id">
                            <option 
                                :value="department.id" 
                                :selected="item.department.id == department.id"
                                v-for="department in user.departments" 
                                v-bind:key="department.id"
                                >{{ department.name }}</option>
                        </select>
                    </template>

                    <!-- Location -->
                    <template v-slot:item.location="{ item }">
                        <select v-model="item.location_id">
                            <option 
                                :value="location.id" 
                                :selected="item.location.id == location.id"
                                v-for="location in user.locations" 
                                v-bind:key="location.id"
                                >{{ location.name }}</option>
                        </select>
                    </template>

                    <!-- Action Buttons -->
                    <template v-slot:item.action="{ item }">
                        <v-btn
                            small
                            color="success"
                            rounded
                            depressed 
                            class="mr-2"
                            @click="updateUser(item)"
                            :disabled="!unsafed(item) || !validUser(item)"
                        >{{ $t('save') }}</v-btn>
                        
                        <v-tooltip top>
                            <template v-slot:activator="{ on }">
                                <v-icon :disabled="!unsafed(item)" v-on="on" small class="mr-2" @click="resetUser(item)">replay</v-icon>
                            </template>
                            <span>{{ $t('reset') }}</span>
                        </v-tooltip>
                        <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
                        <v-icon small @click="deleteItem(item)">delete</v-icon>
                    </template>

                </v-data-table>

                <v-snackbar v-model="snack" :timeout="snackTimeout" :color="snackColor" top>
                    <span v-html="snackText"></span>
                    <v-btn text @click="snack = false">{{ $t('closer_button') }}</v-btn>
                </v-snackbar>

            </v-card>
        </template>

        <!-- <v-btn v-on:click="getUsers">Get Users</v-btn> -->



    </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import { mask, TheMask } from 'vue-the-mask'
import { setTimeout } from 'timers';


export default {
    middleware: 'canCreateUsers',

    computed: {
        ...mapGetters({
            user: 'auth/user',
            usersCreated: 'users/usersCreated',
            groupsModerating: 'users/groupsModerating'
        }),
    },

    components: {
        TheMask
    },

    directives: {
        mask,
    },

    mounted(){
    },

    data () {
      return {

          dialogEditCompanies: false,
          dialogCreateCompany: false,
          company_name: '',

        panTokens: {
            P: {
                pattern: /[0-9A-Za-z]/,
            }
        },

        usersCreatedOld: [],

        loading: false,
        search: '',
        headers: [
          {
            text: 'ID',
            align: 'left',
            value: 'id',
          },
          { text: this.$t('PAN'), value: 'pan.pan' },
          { text: this.$t('PIN'), value: 'pan.pin' },
          { text: this.$t('groups'), value: 'groups'},
          { text: this.$t('company'), value: 'company'},
          { text: this.$t('department'), value: 'department'},
          { text: this.$t('location'), value: 'location'},
          { text: this.$t('updated_at'), value: 'updated_at'},
          { text: this.$t('created_at'), value: 'created_at'},
        //   { text: 'Locked', value: 'pan.locked_until'},
          { text: 'Actions', value: 'action', sortable: false },
        ],

        // Company Headers
        company_headers: [
          { text: this.$t('id'), value: 'id' },
          { text: this.$t('name'), value: 'name' },
          { text: this.$t('public'), value: 'public' },
          { text: this.$t('created_by'), value: 'created_by' },
          { text: this.$t('updated_at'), value: 'updated_at'},
          { text: this.$t('created_at'), value: 'created_at'},
        ],

        all_groups: [],

        maxPanChars: v => v.length <= 6 || 'Input too long!',
        maxPinChars: v => v.length == 4 || 'Pin Wrong!',

        // Snackbar
        snack: false,
        snackColor: '',
        snackText: '',
        snackTimeout: 3000,

      }
    },

    watch: {
        usersCreated(promise) {
            // save Promise result in local state
            this.usersCreatedOld = JSON.parse(JSON.stringify(promise));
        }
    },

    created: function() {        
        this.$store.dispatch('users/fetchUsersCreated')
        this.$store.dispatch('users/fetchGroupsModerating')

    },

    methods: {

        validUser(item) {
            if(
                this.pinIsOk(item) && 
                this.panIsOk(item)
            ) {
                return true;
            }
            return false;
        },

        pinIsOk(item) {
            return item.pan.pin.length == 4;
        },

        panIsOk(item) {
            return item.pan.pan.length == 6;
        },

        getOldUsersKey(item) {
            return this.usersCreated.map(function(x) {
                return x.id;
            }).indexOf(item.id);
        },

        copyObject(obj) {
            return JSON.parse(JSON.stringify(obj));
        },

        unsafed(item) {
            var key = this.getOldUsersKey(item);
            var itemLeft = this.copyObject(item);
            var itemRight = this.copyObject(this.usersCreatedOld[key]);

            delete itemLeft.groupDialog;
            delete itemRight.groupDialog;

            return JSON.stringify(itemLeft) != JSON.stringify(itemRight);
        },

        resetUser(item) {
            var key = this.getOldUsersKey(item);
            Object.assign(item, JSON.parse(JSON.stringify(this.usersCreatedOld[key])))
        },

        updateUser(user) {
            // Variables
            var _this = this;
            this.loading = true;

            // Update User
            this.$store.dispatch('users/updateUser', {
                user: user
            }).then(function(e) {
                // Success
                if(!e || !e.reponse || !e.reponse.data || !e.response.data.error) {
                    _this.snackTimeout = 3000;
                    _this.snack = true;
                    _this.snackColor = 'success'
                    _this.snackText = _this.$t('data_saved')

                    // Save Old
                    var key = _this.getOldUsersKey(user);
                    Object.assign(_this.usersCreatedOld[key], JSON.parse(JSON.stringify(user)))
                }
                _this.loading = false;
            }).catch(function(e) {
                // Error
                var res = e.response.data;
                var err = res.error;
                var errText = '';
                for (var e in err) {
                    errText += ': ' + err[e];
                }

                _this.snackTimeout = 6000;
                _this.snack = true;
                _this.snackColor = 'error';
                _this.snackText = _this.$t('attribute_unsaved') + "<br />" + errText;

                _this.loading = false;
            });
        },

        save() {
            this.snackTimeout = 3000;
            this.snack = true
            this.snackColor = 'primary'
            this.snackText = this.$t('attribute_changed')
        },
        saveCompany(item) {
            this.snackTimeout = 5000;
            this.snack = true
            this.snackColor = 'success'
            this.snackText = this.$t('data_saved')

            axios.patch('/api/company/update', {
                id: item.id,
                name: item.name
            });
        },

        createCompany(name) {
            var _this = this;
            axios.post('/api/company/create', {
                name: name
            }).then(function (response) {
                _this.user.companies.push(response.data);
            });
        },

        cancel () {
            this.snackTimeout = 3000;
            this.snack = true
            this.snackColor = 'primary'
            this.snackText = this.$t('canceled')
        },
        open () {
        },
        close () {
          // When Edit Dialog Closed
        },


        changePan(item) {
            item.pan.pan = item.pan.pan.toUpperCase();
            item.pan.pan = item.pan.pan.replace(' ', '');
        },

        changePin(item) {

        },

        alreadyInGroups(group, groups) {
            // item.groupsModerating && item.groupsModerating.includes(group)
            if (groups.filter(function(e) { return e.id === group.id; }).length > 0) {
                return true;
            }
            return false;
        },

        async addCreatedUserToGroup(user, group) {
            user.groups.push(group);
        },

        async removeCreatedUserFromGroup(user, index) {
            user.groups.splice(index, 1);
        },



    }

}

</script>
