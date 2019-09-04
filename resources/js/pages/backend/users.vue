<template>
    <div>
        <h1>Users</h1>
        <p>Create Users with PAN & PIN</p>
        <p>
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
                    { text: this.$t('id'), value: 'id' },
                    { text: this.$t('name'), value: 'name' },
                    { text: this.$t('description_public'), value: 'description_public' },
                    { text: this.$t('description_mods'), value: 'description_mods' },
                    { text: this.$t('updated_at'), value: 'updated_at'},
                    { text: this.$t('created_at'), value: 'created_at'}
                ]" />
        </p>

        <br>

        <template>
            <v-card>
                <v-card-title>
                    Created Users
                    <div class="flex-grow-1"></div>
                </v-card-title>
                <v-card-text> 
                    <!-- <v-row no-gutters align-content="end" align="end" justify="end"> 
                        <v-col cols="12" md="6">
                            <v-btn depressed color="primary">Benutzer erstellen</v-btn>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details></v-text-field>
                        </v-col>
                    </v-row> -->
                </v-card-text>
                    <!-- No Select Toolbar -->
                    <v-toolbar class="coha--toolbar" v-if="selected.length <= 0"  :flat="search == ''" floating min-height="85px" height="auto">
                        <!-- Create User -->
                            <v-dialog v-model="bCreateUsersDialog" transition="dialog-bottom-transition" max-width="700" :content-class="bCreateUsersLoading ? 'naked dark centered': '' " persistent>
                              <template v-slot:activator="{ on }">
                                  <!-- Create User: Button -->
                                <v-btn rounded text class="primary--text" v-on="on">
                                    <v-icon left class="pr-2">mdi-account-plus</v-icon> Benutzer erstellen
                                </v-btn>
                              </template>

                              <!-- Create User: Modal -->
                              <v-card v-if="!bCreateUsersLoading">
                                <v-toolbar dark color="primary">
                                  <v-btn icon dark @click="bCreateUsersDialog = false">
                                    <v-icon>mdi-close</v-icon>
                                  </v-btn>
                                  <v-toolbar-title>Erstelle neue Zugänge</v-toolbar-title>
                                  <div class="flex-grow-1"></div>
                                  <v-toolbar-items>
                                    <v-btn dark text @click="createUsers()" v-if="iCreateUsersNumber > 0">{{ iCreateUsersNumber }} Zugänge Generieren</v-btn>
                                  </v-toolbar-items>
                                </v-toolbar>
                                <v-list three-line subheader>
                                  <v-subheader>Zugänge Konfigurieren</v-subheader>
                                  <v-list-item>
                                    <v-list-item-content>
                                      <v-list-item-title>Benutzer / Zugänge erstellen</v-list-item-title>
                                      <v-list-item-subtitle>Wählen Sie eine Anzahl der zu erstellenden Benutzer aus</v-list-item-subtitle>
                                    </v-list-item-content>
                                  </v-list-item>

                                  <v-list-item>
                                    <v-list-item-content style="max-width:400px;">
                                        <v-text-field outlined clearable label="Benutzeranzahl" v-model="iCreateUsersNumber" required hide-details type="number"></v-text-field>
                                    </v-list-item-content>
                                  </v-list-item>

                                  <v-list-item>
                                    <v-list-item-action>
                                      <v-checkbox v-model="bCreateUsersRandomPin" color="primary"></v-checkbox>
                                    </v-list-item-action>
                                    <v-list-item-content>
                                      <v-list-item-title>Zufällige PIN</v-list-item-title>
                                      <v-list-item-subtitle>Generiert eine Zufällige PIN. z.B. 1534 oder 5664. Die PIN kann im Anschluss angepasst werden</v-list-item-subtitle>
                                    </v-list-item-content>
                                  </v-list-item>

                                  <v-list-item>
                                    <v-list-item-action>
                                      <v-checkbox v-model="bCreateUsersRandomPan" disabled color="primary"></v-checkbox>
                                    </v-list-item-action>
                                    <v-list-item-content>
                                      <v-list-item-title>Zufällige PAN</v-list-item-title>
                                      <v-list-item-subtitle>Generiert eine Zufällige und individuelle / einmalige PAN. z.B.: G4D 4Y6. Die PAN kann im Anschluss angepasst werden</v-list-item-subtitle>
                                    </v-list-item-content>
                                  </v-list-item>

                                </v-list>
                                <v-divider></v-divider>
                              </v-card>
                              <!-- Loading -->
                              <template v-else>
                                <p>{{ $t('loading.text') }}</p>
                                <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
                              </template>
                            </v-dialog>

                            <v-switch class="mt-6 ml-6" v-model="showPin" :label="showPin ? 'PIN ist sichtbar' : 'PIN ist versteckt'" color="primary"></v-switch>

                        <div class="flex-grow-1"></div>
                        <v-text-field style="max-width: 400px;" v-model="search" :label="$t('Search')" autocomplete="off"  append-icon="search" single-line hide-details></v-text-field>
                    </v-toolbar>
                    <!-- Toolbar for Selections -->
                    <v-toolbar class="coha--toolbar" v-else :flat="search == ''" color="primary"  dark floating min-height="85px" height="auto">
                        <v-btn color="success" rounded @click="updateUsers()">
                            <v-icon left>mdi-content-save</v-icon> {{ selected.length + ' ' + $t('save') }}
                        </v-btn>
                        <v-btn text rounded>
                            <v-icon left>mdi-pencil</v-icon> {{ selected.length + ' ' + $t('edit') }}
                        </v-btn>

                        <Print :users="selected" />
                        
                        <v-btn text rounded error warning @click.stop="deleteUsersDialog = true">
                            <v-icon left>delete</v-icon> {{ selected.length + ' ' + $t('delete') }}
                        </v-btn>
                        <v-dialog v-model="deleteUsersDialog" max-width="290" dark content-class="naked dark centered">
                            <h2 class="display-2">Delete?</h2>
                            <p>Delete?</p>
                            <v-container fluid>
                                <v-row align="center">
                                    <v-col class="text-center" cols="12" sm="12">
                                        <v-btn depressed @click.stop="deleteUsersDialog = false" outlined>Abbruch</v-btn>
                                        <v-btn depressed @click="deleteUsers(selected)" color="error">Zugänge Löschen!</v-btn>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-dialog>
                        
                        <v-switch class="mt-6 ml-6" v-model="showPin" :label="showPin ? 'PIN ist sichtbar' : 'PIN ist versteckt'" color="accent"></v-switch>
                        <div class="flex-grow-1"></div>
                    </v-toolbar>

                <v-data-table
                    v-if="usersCreated && usersCreated.length >= 1"
                    :headers="headers"
                    :items="usersCreated"
                    v-model="selected"
                    :search="search"
                    show-select 
                    multi-sort 
                    :loading="loading"
                    :loading-text="$t('loading.text')"
                    >

                    <!-- PAN -->
                    <template v-slot:item.pan.pan="{ item }">
                        <template v-if="item.pan && item.pan.pan">
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
                                    <template v-if="item.pan.pan">
                                        <span :class="!panIsOk(item) ? 'red--text' : ''">
                                            <span class="pan--part">{{ item.pan.pan.substring(0,3) }}</span><span class="pan--part">{{ item.pan.pan.substring(3,6) }}</span>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span style="text-transform: uppercase;" class="red--text">{{ $t('empty') }}</span>
                                    </template>
                                    <template v-slot:input>
                                        <div class="pan--dialog-input c-code-text">
                                            <v-text-field 
                                                v-model="item.pan.pan"
                                                :rules="[maxPanChars]"
                                                :label="$t('edit')"
                                                single-line
                                                v-on:change="changePan(item)" 
                                                :error="!panIsOk(item)" 
                                                :disabled="panIsLoading"
                                                :loading="panIsLoading"
                                                counter>
                                                <v-tooltip slot="append" top>
                                                    <template #activator="{ on }">
                                                        <v-icon class="mr-1" v-on="on" @click="getRandomPan(item)">mdi-repeat</v-icon>
                                                    </template>
                                                    <span>Zufällige PAN generieren</span>
                                                </v-tooltip>
                                            </v-text-field>
                                        </div>
                                    </template>
                                </v-edit-dialog>
                            </span>   
                        </template>                    
                    </template>


                    <!-- PIN -->
                    <template v-slot:item.pan.pin="{ item }">
                        <template v-if="item.pan && item.pan.pin">
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
                                            <span :class="!pinIsOk(item) ? 'red--text' : ''">{{ showPin ? item.pan.pin : '****' }}</span>
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
                                                autocomplete="off">
                                                <v-tooltip slot="append" top>
                                                    <template #activator="{ on }">
                                                        <v-icon class="mr-1" v-on="on" @click="generateRandomPin(item)">mdi-repeat</v-icon>
                                                    </template>
                                                    <span>Zufällige PIN generieren</span>
                                                </v-tooltip>    
                                            </v-text-field>
                                        </div>
                                    </template>
                                </v-edit-dialog>
                            </span>
                        </template>
                    </template>

                    <!-- Groups -->
                    <template v-slot:item.groups="{ item }">
                        <template v-if="user.groups_moderating && user.groups_moderating.length >= 1">
                            <template v-for="(group, i) in item.groups">
                                <span v-bind:key="i">
                                    <v-chip small outlined v-if="user.groups_moderating.find(x => x.id === group.id)" class="mr-1 mt-1 mb-1">
                                        {{ user.groups_moderating.find(x => x.id === group.id).name }}
                                    </v-chip>
                                    <v-chip small outlined disabled v-else class="mr-1 mt-1 mb-1">
                                        {{ group.name }}
                                    </v-chip>
                                </span>
                            </template>

                            <!-- Gruppen hinzufügen / entfernen -->
                            <template>
                                <v-dialog v-model="item.groupDialog" scrollable max-width="500px">
                                    <template v-slot:activator="{ on }">
                                        <v-chip v-on="on" small :disabled="item.groupDialog" class="mt-1 mb-1">{{ $t('edit') }}</v-chip>
                                    </template>
                                    <v-card>
                                        <v-card-title>Gruppen hinzufügen / entfernen</v-card-title>
                                        <v-divider></v-divider>

                                        <v-card-text style="height: 500px;">
                                            <p>Der Gewählte Nutzer mit der ID "{{ item.id }}" und mit der PAN "{{ item.pan && item.pan.pan ? item.pan.pan : '' }}"</p>
                                            <v-card outlined>
                                                <v-list subheader two-line flat>
                                                    <v-subheader>Nutzer ist in Gruppen:</v-subheader>
                                                    <template v-if="item.groups">
                                                        <template  v-for="(group, i) in item.groups">
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
                                                    </template>
                                                </v-list>
                                            </v-card>

                                            <br>

                                            <v-card outlined>
                                                <v-list subheader two-line flat>
                                                    <v-subheader>Nutzer für diese Gruppen hinzufügen</v-subheader>
                                                    <template v-if="user.groups_moderating">
                                                        <template v-for="(group, i) in user.groups_moderating">
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

                                                                <v-divider v-if="i+1 < user.groups_moderating.length" inset></v-divider>
                                                            </div>
                                                        </template>
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
                            <option disabled>Bitte auswählen</option>
                            <option 
                                :value="company.id" 
                                :selected="(item.company && item.company.id == company.id)"
                                v-for="company in user.companies" 
                                v-bind:key="company.id"
                                >{{ company.name }}</option>
                        </select>
                    </template>

                    <!-- Department -->
                    <template v-slot:item.department="{ item }">
                        <select v-model="item.department_id">
                            <option disabled>Bitte auswählen</option>
                            <option 
                                :value="department.id" 
                                :selected="(item.department && item.department.id == department.id)"
                                v-for="department in user.departments" 
                                v-bind:key="department.id"
                                >{{ department.name }}</option>
                        </select>
                    </template>

                    <!-- Location -->
                    <template v-slot:item.location="{ item }">
                        <select v-model="item.location_id">
                            <option disabled>Bitte auswählen</option>
                            <option 
                                :value="location.id" 
                                :selected="(item.location && item.location.id == location.id)"
                                v-for="location in user.locations" 
                                v-bind:key="location.id"
                                >{{ location.name }}</option>
                        </select>
                    </template>

                    <!-- Action Buttons -->
                    <template v-slot:item.action="{ item }">
                        <div style="white-space: nowrap;">
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

                            <v-dialog v-model="item.deleteUserDialog" max-width="290" dark content-class="naked dark centered" transition="dialog-bottom-transition">
                                    <template v-slot:activator="{ on }">
                                        <v-icon small v-on="on">delete</v-icon>
                                    </template>
                                    <h2 class="display-2">Delete?</h2>
                                    <p>Delete?</p>
                                    <v-container fluid>
                                        <v-row align="center">
                                            <v-col class="text-center" cols="12" sm="12">
                                                <v-btn depressed @click.stop="item.deleteUserDialog = false" outlined>Abbruch</v-btn>
                                                <v-btn depressed @click="deleteUsers([item])" color="error">Zugang Löschen!</v-btn>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                            </v-dialog>


                        </div>
                    </template>

                </v-data-table>

                <v-snackbar v-model="snack" :timeout="snackTimeout" :color="snackColor" top>
                    <span v-html="snackText"></span>
                    <v-btn text @click="snack = false">{{ $t('closer_button') }}</v-btn>
                </v-snackbar>

            </v-card>
        </template>

    </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import { mask, TheMask } from 'vue-the-mask'
import { setTimeout } from 'timers';
import UserDataModal from '~/components/BackendUserDataModal'
import Print from '~/components/BackendUsersPrint'

export default {
    middleware: 'canCreateUsers',

    computed: {
        ...mapGetters({
            user: 'auth/user',
            usersCreated: 'users/usersCreated',
        }),
    },

    components: {
        TheMask,
        UserDataModal,
        Print
    },

    directives: {
        mask,
    },

    mounted(){
    },

    data () {
      return {

          pinLength: 4,
          panLength: 6,
          panTokens: {
              P: {
                  pattern: /[0-9A-Za-z]/,
              }
          },
          panIsLoading: false,
  
          usersCreatedOld: [],
          selected: [],
  
          loading: false,
          showPin: false,
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
  
          all_groups: [],
  
          maxPanChars: v => v && v.length <= 6 || 'Input too long!',
          maxPinChars: v => v && v.length == 4 || 'Pin Wrong!',
  
          // Snackbar
          snack: false,
          snackColor: '',
          snackText: '',
          snackTimeout: 3000,
  
          // Create Users
          iCreateUsersNumber: 5,
          bCreateUsersDialog: false,
          bCreateUsersLoading: false,
          bCreateUsersRandomPan: true,
          bCreateUsersRandomPin: true,

          deleteUsersDialog: false,

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

        getRandomPan(user) {
            var _this = this;
            _this.panIsLoading = true;

            axios.get('/api/get-random-pan')
            .then(function(response) {
                _this.panIsLoading = false;
                user.pan.pan = response.data;
            }).catch(function(response) {
                _this.panIsLoading = false;
            });
        },

        generateRandomPin(item) {
            if(item && item.pan && item.pan.pin) {
                item.pan.pin = Math.random().toString().substr(2, this.pinLength);
            }
        },

        createUsers() {
            var _this = this;

            // Close Dialog and Load
            this.bCreateUsersLoading = true;

            // Create Users
            this.$store.dispatch('users/createUsers', {
                number: this.iCreateUsersNumber
            }).then(function(response) {
                _this.bCreateUsersLoading = false;
                _this.bCreateUsersDialog = false;

                // Success
                var users = response.data;
                for (var i in users) {
                    var oUser = users[i];
                    oUser.isSelected = true;

                    // Add to Array
                    _this.usersCreated.unshift(oUser);
                    _this.selected.unshift(oUser);
                }
            }).catch(function(response) {
                // ERROR
                _this.bCreateUsersLoading = false;
                _this.bCreateUsersDialog = false;
            });
            
        },

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
            return item.pan && item.pan.pin && item.pan.pin.length == this.pinLength;
        },

        panIsOk(item) {
            return item.pan && item.pan.pan && item.pan.pan.length == this.panLength;
        },

        getOldUsersId(item) {
            return this.usersCreated.map(function(x) {
                return x.id;
            }).indexOf(item.id);
        },

        copyObject(obj) {
            return JSON.parse(JSON.stringify(obj));
        },

        unsafed(item) {
            var key = this.getOldUsersId(item);
            var itemLeft = this.copyObject(item);
            var itemRight = this.copyObject(this.usersCreatedOld[key]);

            delete itemLeft.groupDialog;
            delete itemLeft.deleteUserDialog;
            delete itemRight.groupDialog;
            delete itemRight.deleteUserDialog;

            return JSON.stringify(itemLeft) != JSON.stringify(itemRight);
        },

        resetUser(item) {
            var key = this.getOldUsersId(item);
            Object.assign(item, JSON.parse(JSON.stringify(this.usersCreatedOld[key])))
        },

        updateUsers() {
            var users = this.selected;
            for (var i in users) {
                var user = users[i];
                this.updateUser(user);
            }
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
                if(!e || !e.response || !e.response.data || !e.response.data.error) {
                    _this.snackTimeout = 3000;
                    _this.snack = true;
                    _this.snackColor = 'success'
                    _this.snackText = _this.$t('data_saved')

                    // Save Old
                    var key = _this.getOldUsersId(user);
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

        findKeyById(arr, item) {
            return arr.findIndex(x => x.id === item.id);
        },

        deleteUsers(users) {
            // Variables
            var _this = this;
            this.loading = true;
            this.deleteUsersDialog = false;

            // Delete User
            this.$store.dispatch('users/deleteUsers', {
                ids: users.map(user => user.id)
            }).then(function(e) {
                // Success
                _this.loading = false;
                _this.snackTimeout = 3000;
                _this.snack = true;
                _this.snackColor = 'success'
                _this.snackText = _this.$t('data_saved')

                for (var i in users) {
                    // Get ID 
                    var user = users[i];

                    // Find Indexes
                    var index1 = _this.findKeyById(_this.usersCreated, user);
                    var index2 = _this.findKeyById(_this.usersCreatedOld, user);

                    // Delete Frontend with Old
                    _this.usersCreated.splice(index1, 1);
                    _this.usersCreatedOld.splice(index2, 1);
                }

            });
        },

        save() {
            this.snackTimeout = 3000;
            this.snack = true
            this.snackColor = 'primary'
            this.snackText = this.$t('attribute_changed')
        },

        cancel () {
            this.snackTimeout = 3000;
            this.snack = true
            this.snackColor = 'primary'
            this.snackText = this.$t('canceled')
        },
        open () {
            // When Edit Dialog Open
        },
        close () {
            // When Edit Dialog Closed
        },


        changePan(item) {
            if(item.pan && item.pan.pan) {
                item.pan.pan = item.pan.pan.toUpperCase();
                item.pan.pan = item.pan.pan.replace(' ', '');
            }
        },

        changePin(item) {

        },

        alreadyInGroups(group, groups) {
            if(!group || !groups || !groups.filter) return;
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
