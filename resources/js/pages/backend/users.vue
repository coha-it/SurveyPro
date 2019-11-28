<template>
    <div>
        <h1>Users</h1>
        <p>Create Users with PAN & PIN</p>
        <p>
                <!-- Create User -->

                <q-btn unelevated color="primary" @click="bCreateUsersDialog = true" icon-right="mdi-account-plus">
                    Benutzer erstellen&nbsp;&nbsp;&nbsp;
                </q-btn>

                <q-dialog v-model="bCreateUsersDialog" max-width="700" persistent>
                  <!-- Create User: Modal -->
                  <q-card v-if="!bCreateUsersLoading">
                    <q-toolbar class="bg-primary text-white" dark color="primary">
                      <q-btn flat round dense class="q-mr-sm" icon="mdi-close" dark @click="bCreateUsersDialog = false"></q-btn>
                      <q-toolbar-title>Erstelle neue Zugänge</q-toolbar-title>
                      <div class="flex-grow-1"></div>
                      <q-btn dark flat class="q-mr-sm" @click="createUsers()" v-if="iCreateUsersNumber > 0">{{ iCreateUsersNumber }} Zugänge Generieren</q-btn>
                    </q-toolbar>
                    <q-list three-line subheader>
                      <q-item>
                        <q-item-section>
                          <q-item-label>Benutzer / Zugänge erstellen</q-item-label>
                          <q-item-label caption>Wählen Sie eine Anzahl der zu erstellenden Benutzer aus</q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-item>
                        <q-item-section style="max-width:400px;">
                            <q-input outlined clearable label="Benutzeranzahl" v-model="iCreateUsersNumber" required hide-details type="number" />
                        </q-item-section>
                      </q-item>

                      <q-item tag="label" v-ripple>
                        <q-item-section side top>
                          <q-checkbox v-model="bCreateUsersRandomPin" />
                        </q-item-section>

                        <q-item-section>
                          <q-item-label>Zufällige PIN</q-item-label>
                          <q-item-label caption>
                            Generiert eine Zufällige PIN. z.B. 1534 oder 5664. Die PIN kann im Anschluss angepasst werden
                          </q-item-label>
                        </q-item-section>
                      </q-item>


                      <q-item tag="label" disable>
                        <q-item-section side top>
                          <q-checkbox v-model="bCreateUsersRandomPan" disable />
                        </q-item-section>

                        <q-item-section>
                          <q-item-label>Zufällige PAN</q-item-label>
                          <q-item-label caption>
                            Generiert eine Zufällige und individuelle / einmalige PAN. z.B.: G4D 4Y6. Die PAN kann im Anschluss angepasst werden
                          </q-item-label>
                        </q-item-section>
                      </q-item>

                    </q-list>
                    <q-separator></q-separator>
                  </q-card>
                  <!-- Loading -->
                  <template v-else>
                    <p>{{ $t('loading.text') }}</p>
                    <q-progress-linear indeterminate color="white" class="mb-0"></q-progress-linear>
                  </template>
                </q-dialog>

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

        </p>

        <br>

        <template>
            <q-card>
                <q-card-section>
                  <div class="text-h6">Created Users</div>
                  <div class="text-subtitle2">All your Created Users</div>
                </q-card-section>
                <q-card-section>
                    <!-- <q-row no-gutters align-content="end" align="end" justify="end">
                        <q-col cols="12" md="6">
                            <q-btn depressed color="primary">Benutzer erstellen</q-btn>
                        </q-col>
                        <q-col cols="12" md="6">
                            <q-input v-model="search" append-icon="search" label="Search" single-line hide-details />
                        </q-col>
                    </q-row> -->
                </q-card-section>

                <q-table
                    v-if="usersCreated && usersCreated.length >= 1"
                    :columns="headers"
                    :data="usersCreated"
                    :filter="search"
                    :filter-method="myFilter"
                    selection="multiple"
                    bordered
                    :sort-method="customSort"
                    separator="cell"

                    show-select
                    :loading="loading"
                    :loading-text="$t('loading.text')"
                    :items-per-page="parseInt(itemsPerPage)"
                    :footer-props="{
                        showFirstLastPage: true,
                    }"
                    dense
                    :selected.sync="selected"
                    class="my-sticky-header-table"
                    >

                    <!-- Toolbars -->
                    <template v-slot:top="props">
                      <!-- No Select Toolbar -->
                      <q-toolbar class="coha--toolbar" v-if="selected.length <= 0"  :flat="search == ''" floating min-height="85px" height="auto">
                          <!-- <q-toggle class="mt-6 mr-6" v-model="bExtendedFilter" :label="'Erweitert Filtern'" color="primary" /> -->
                          <q-toggle class="mt-6 mr-6" v-model="showPin" :label="'PIN Zeigen'" color="red" />
                          <div class="flex-grow-1"></div>
                          <q-toolbar-title></q-toolbar-title>
                          <q-input outlined style="max-width: 400px;" v-model="search" :label="$t('Search')" autocomplete="off"  append-icon="search" hide-details outline />
                          &nbsp;
                          <q-input outlined v-model="itemsPerPage" number type="number" hide-details style="max-width: 150px;" label="Zeilen pro Seite" class="ml-5" outline  />
                          <q-btn
                            flat round dense
                            :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                            @click="props.toggleFullscreen"
                            class="q-ml-md"
                          />
                      </q-toolbar>
                      <!-- Toolbar for Selections -->
                      <q-toolbar class="coha--toolbar bg-primary text-white" v-else :flat="search == ''" color="primary"  dark floating min-height="85px" height="auto">

                          <q-btn color="success" rounded @click="updateUsers(getUnsaved(selected))" :disabled="!getUnsaved(selected).length" icon-left="content-save">
                              {{ getUnsaved(selected).length + ' ' + $t('save') }}
                          </q-btn>

                          <q-menu offset-y>
                              <template v-slot:activator="{ on:menuedit }">
                                  <q-btn text rounded v-on="{ ...menuedit }">
                                      <q-icon left>mdi-pencil</q-icon> {{ selected.length + ' ' + $t('edit') }}
                                  </q-btn>
                              </template>
                              <q-list>
                                  <BulkGroupChanges
                                      :aItems="user.groups_moderating"
                                      :selected="selected"
                                      />

                                  <!-- Menu: Company -->
                                  <!-- <BulkProfileChanges
                                      :aItems="user.companies"
                                      :selected="selected"
                                      sId="company_id"
                                      sModel="company"
                                      title="Ändere Firma"
                                      label="Firma wählen"
                                      menuText="Firma"
                                      /> -->

                                  <!-- Menu: Department -->
                                  <!-- <BulkProfileChanges
                                      :aItems="user.departments"
                                      :selected="selected"
                                      sId="department_id"
                                      sModel="department"
                                      title="Ändere Abteilung"
                                      label="Abteilung wählen"
                                      menuText="Abteilung"
                                      /> -->

                                  <!-- Menu: Location -->
                                  <!-- <BulkProfileChanges
                                      :aItems="user.locations"
                                      :selected="selected"
                                      sId="location_id"
                                      sModel="location"
                                      title="Ändere Ort"
                                      label="Ort wählen"
                                      menuText="Ort"
                                      /> -->

                              </q-list>
                          </q-menu>


                          <Print :users="selected" :disabled="getUnsaved(selected).length > 0 ? true : false" />

                          <q-btn text rounded error warning @click.stop="deleteUsersDialog = true">
                              <q-icon left>delete</q-icon> {{ selected.length + ' ' + $t('delete') }}
                          </q-btn>

                          <q-dialog v-model="deleteUsersDialog" max-width="290" dark content-class="naked dark centered">
                              <h2 class="display-2">Delete?</h2>
                              <p>Delete?</p>
                              <q-container fluid>
                                  <q-row align="center">
                                      <q-col class="text-center" cols="12" sm="12">
                                          <q-btn depressed @click.stop="deleteUsersDialog = false" outline>Abbruch</q-btn>
                                          <q-btn depressed @click="deleteUsers(selected)" color="error">Zugänge Löschen!</q-btn>
                                      </q-col>
                                  </q-row>
                              </q-container>
                          </q-dialog>

                          <!-- <q-checkbox left-label  class="mt-6 ml-6" v-model="bExtendedFilter" :label="'Erweitert Filtern'" color="accent"></q-checkbox> -->
                          <q-checkbox left-label class="mt-6 ml-6" v-model="showPin" :label="showPin ? 'PIN ist sichtbar' : 'PIN ist versteckt'" color="accent"></q-checkbox>
                          <div class="flex-grow-1"></div>
                          <q-input v-model="itemsPerPage" type="number" number hide-details style="max-width: 150px;" label="Zeilen pro Seite" class="ml-5" outline />
                          <q-btn
                            flat round dense
                            :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                            @click="props.toggleFullscreen"
                            class="q-ml-md"
                          />
                      </q-toolbar>


                      <q-container fluid v-if="bExtendedFilter && false">
                          <q-row>
                              <q-col>
                                  <q-checkbox v-model="bShowDeletedUsers" color="primary" label="Gelöschte Nutzer zeigen"></q-checkbox>
                              </q-col>
                          </q-row>
                      </q-container>
                    </template>

                    <!-- PAN -->
                    <template v-slot:body-cell-pan="props">
                      <q-td :props="props">
                        <div class="pan--dialog-input c-code-text">
                          <template v-if="props && props.row && props.row.pan.pan && props.row.pan.pan.length > 0">
                              <span :class="!panIsOk(props.row) ? 'red--text' : ''">
                                  <span class="pan--part">{{ props.row.pan.pan.substring(0,3) }}</span><span class="pan--part">
                                    {{ props.row.pan.pan.substring(3,6) }}
                                  </span>
                              </span>
                          </template>
                          <template v-else>
                              <span style="text-transform: uppercase;" class="text-red c-code-text">{{ $t('empty') }}</span>
                          </template>
                          <q-popup-edit
                              v-model="props.row.pan.pan"
                              buttons
                              @save="save(props.row)"
                              @cancel="cancel"
                              @open="open"
                              @close="close"
                              single-line
                              :label="$t('edit')"
                              persistent
                              :cover="false"
                              self="center left"
                              anchor="center right"
                              :offset="[5, 0]"
                            >
                            <span class="coha--list-item pan c-code-text ">
                              <q-input
                                v-model="props.row.pan.pan"
                                dense
                                autofocus
                                counter
                                persistent-hint
                                :rules="[maxPanChars]"
                                v-on:keyup="changePan(props.row)"
                                v-on:change="changePan(props.row)"
                                :error="!panIsOk(props.row)"
                                :disabled="panIsLoading"
                                :loading="panIsLoading"
                                required
                                maxlength="6"
                              >
                                <template v-slot:append>
                                  <q-btn dense flat round icon="repeat" @click="getRandomPan(props.row)"></q-btn>
                                  <q-tooltip>Zufällige PAN generieren</q-tooltip>
                                </template>
                              </q-input>
                            </span>
                          </q-popup-edit>
                        </div>
                      </q-td>
                    </template>


                    <!-- PIN -->
                    <template v-slot:body-cell-pin="props">
                      <q-td :props="props">
                        <div class="pin--dialog-input c-code-text">
                          <template v-if="props && props.row && props.row.pan.pin && props.row.pan.pin.length > 0">
                              <span :class="!pinIsOk(props.row) ? 'red--text' : ''">
                                {{ showPin ? props.row.pan.pin : '****' }}
                              </span>
                          </template>
                          <template v-else>
                              <span style="text-transform: uppercase;" class="text-red c-code-text">{{ $t('empty') }}</span>
                          </template>

                          <q-popup-edit
                              v-model="props.row.pan.pin"
                              buttons
                              @save="save(props.row)"
                              @cancel="cancel"
                              @open="open"
                              @close="close"
                              single-line
                              :label="$t('edit')"
                              persistent
                              :cover="false"
                              self="center left"
                              anchor="center right"
                              :offset="[5, 0]"
                            >
                            <span class="coha--list-item pin c-code-text ">
                              <q-input
                                v-model="props.row.pan.pin"
                                dense
                                autofocus
                                counter
                                persistent-hint
                                :rules="[maxPinChars]"
                                v-on:keyup="changePin(props.row)"
                                v-on:change="changePin(props.row)"
                                :error="!pinIsOk(props.row)"
                                required
                                autocomplete="off"
                                type="text"
                                pattern="[0-9]*"
                                name="pin"
                                v-mask="'####'"
                                maxlength="4"
                              >
                                <template v-slot:append>
                                  <q-btn dense flat round icon="repeat" @click="generateRandomPin(props.row)"></q-btn>
                                  <q-tooltip>Zufällige PIN generieren</q-tooltip>
                                </template>
                              </q-input>
                            </span>
                          </q-popup-edit>
                        </div>
                      </q-td>
                    </template>


                    <!-- Groups -->
                    <template v-slot:body-cell-groups="props">
                      <q-td :props="props">
                        <template v-if="user.groups_moderating && user.groups_moderating.length >= 1">
                            <template v-for="(group, i) in props.row.groups">
                                <span v-bind:key="i">


                                    <q-chip v-if="user.groups_moderating.find(x => x.id === group.id)" small outlined class="mr-1 mt-1 mb-1"
                                        :color="getGroupPivotColor(group)">
                                        {{ user.groups_moderating.find(x => x.id === group.id).name }}
                                        <q-tooltip bottom :open-delay="group.pivot ? group.pivot.is_member ? 1000 : 100 : 100">
                                            <template v-if="group.pivot.is_member">
                                                Teilnehmer
                                            </template>
                                            <template v-if="group.pivot.is_member && group.pivot.is_mod">
                                                &
                                            </template>
                                            <template v-if="group.pivot.is_mod">
                                                Moderator
                                            </template>
                                        </q-tooltip>
                                    </q-chip>
                                    <q-chip v-else small outlined disabled  class="mr-1 mt-1 mb-1">
                                        {{ group.name }}
                                        <q-tooltip bottom :open-delay="group.pivot ? group.pivot.is_member ? 1000 : 100 : 100">
                                          Keine Rolle!
                                        </q-tooltip>
                                    </q-chip>

                                </span>
                            </template>

                            <!-- Gruppen hinzufügen / entfernen -->
                            <template>

                                <div>Bearbeiten</div>

                                <q-popup-edit v-model="props.row">
                                    <q-card>
                                        <q-card-title>Gruppen hinzufügen / entfernen</q-card-title>
                                        <q-separator />

                                        <q-card-text style="height: 500px;">
                                            <p>Der Gewählte Nutzer mit der ID "{{ props.row.id }}" und mit der PAN "{{ props.row.pan && props.row.pan.pan ? props.row.pan.pan : '' }}"</p>
                                            <q-card outlined>
                                                <v-list subheader two-line flat>
                                                    <v-subheader>Nutzer ist in Gruppen:</v-subheader>
                                                    <template v-if="props.row.groups">
                                                        <template  v-for="(group, i) in props.row.groups">
                                                            <div v-bind:key="group.id">

                                                                <v-list-item>
                                                                    <v-list-item-avatar>
                                                                        <v-icon class="white--text primary">mdi-account-multiple</v-icon>
                                                                    </v-list-item-avatar>

                                                                    <v-list-item-content>
                                                                        <v-list-item-title>{{ group.name }}</v-list-item-title>
                                                                        <v-list-item-subtitle>{{ group.description_public || 'Ohne Gruppenbeschreibung'}}</v-list-item-subtitle>
                                                                        <p>
                                                                            <template v-if="group.pivot">
                                                                                <v-checkbox v-model="group.pivot.is_member" color="primary" dense hide-details label="Teilnehmer" :true-value="1" :false-value="0"></v-checkbox>
                                                                                <v-checkbox v-model="group.pivot.is_mod" color="red" dense hide-details label="Moderator" :true-value="1" :false-value="0"></v-checkbox>
                                                                            </template>
                                                                        </p>
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
                                                                            @click="removeCreatedUserFromGroup(props.row, i)"
                                                                            >- {{ $t('remove') }}</v-btn>
                                                                    </v-list-item-action>
                                                                </v-list-item>

                                                                <v-divider v-if="i+1 < props.row.groups.length" inset></v-divider>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </v-list>
                                            </q-card>

                                            <br>

                                            <q-card outlined>
                                                <v-list subheader two-line flat>
                                                    <v-subheader>Nutzer für diese Gruppen hinzufügen</v-subheader>
                                                    <template v-if="user.groups_moderating">
                                                        <template v-for="(group, i) in user.groups_moderating">
                                                            <div v-bind:key="group.id">
                                                                <v-list-item :disabled="alreadyInGroups(group, props.row.groups)">
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
                                                                            v-if="alreadyInGroups(group, props.row.groups)"
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
                                                                            @click="addCreatedUserToGroup(item.row, group)">+ {{ $t('add_to') }}</v-btn>
                                                                    </v-list-item-action>
                                                                </v-list-item>

                                                                <v-divider v-if="i+1 < user.groups_moderating.length" inset></v-divider>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </v-list>
                                            </q-card>
                                        </q-card-text>
                                        <q-separator />
                                        <q-card-actions>
                                            <v-btn color="blue darken-1" text @click="item.row.groupDialog = false">{{ $t('closer_button') }}</v-btn>
                                        </q-card-actions>
                                    </q-card>
                                </q-popup-edit>




                            </template>

                        </template>
                      </q-td>
                    </template>

                    <!-- Company -->
                    <template v-slot:item.company="{ item }">
                        <select v-model="item.company_id" @change="companyChanged(item)">
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
                        <select v-model="item.department_id" @change="departmentChanged(item)">
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
                        <select v-model="item.location_id" @change="locationChanged(item)">
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
                                :disabled="isSaved(item) || !validUser(item)"
                            >{{ $t('save') }}</v-btn>

                            <q-tooltip top>
                                <template v-slot:activator="{ on }">
                                    <v-icon :disabled="isSaved(item)" v-on="on" small class="mr-2" @click="resetUser(item)">replay</v-icon>
                                </template>
                                <span>{{ $t('reset') }}</span>
                            </q-tooltip>
                            <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>

                            <q-dialog v-model="item.deleteUserDialog" max-width="290" dark content-class="naked dark centered" transition="dialog-bottom-transition">
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
                            </q-dialog>


                        </div>
                    </template>


                </q-table>

            </q-card>
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
import BulkProfileChanges from '~/components/BackendUserBulkProfileChanges'
import BulkGroupChanges from '~/components/BackendUserBulkGroupChanges'
import MyPopupEdit from '~/components/MyPopupEdit'
import { type } from 'os';

export default {
    middleware: 'canCreateUsers',

    components: {
        TheMask,
        UserDataModal,
        Print,
        BulkProfileChanges,
        BulkGroupChanges,
        MyPopupEdit
    },

    computed: {
        ...mapGetters({
            user: 'auth/user',
            usersCreated: 'users/usersCreated',
        }),

        headers() {
            return [
                {
                  label: 'ID',
                  align: 'left',
                  name: 'id',
                  field: 'id',
                  filter: sWhere => {
                      return this.filterId(this.oFilters.iId, sWhere)
                  },
                  sortable: true,
                },
                {
                  label: this.$t('PAN'),
                  name: 'pan',
                  field: 'pan',
                  filter: sWhere => {
                      return this.filterPan(this.oFilters.sPan, sWhere)
                  },
                  sortable: true,
                },
                {
                  label: this.$t('PIN'),
                  name: 'pin',
                  field: 'pin',
                  filter: sWhere => {
                      return this.filterBasic(this.oFilters.sPin, sWhere)
                  },
                  sortable: true,
                },
                {
                  label: this.$t('groups'),
                  name: 'groups',
                  field: 'groups',
                  sortable: true,
                },
                {
                  label: this.$t('company'),
                  name: 'company',
                  field: 'company',
                  sortable: true,
                },
                {
                  label: this.$t('department'),
                  name: 'department',
                  field: 'department',
                  sortable: true,
                },
                {
                  label: this.$t('location'),
                  name: 'location',
                  field: 'location',
                  sortable: true,
                },
                {
                  label: this.$t('updated_at'),
                  name: 'updated_at',
                  field: 'updated_at',
                  sortable: true,
                },
                {
                  label: this.$t('created_at'),
                  name: 'created_at',
                  field: 'created_at',
                  sortable: true,
                },
                {
                  label: 'Actions',
                  name: 'action',
                  field: 'action',
                  sortable: false
                },
            ];
        }
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
          itemsPerPage: 10,
          search: '',
          bExtendedFilter: false,
          oFilters: {
              sId: '',
              sPan: '',
              sPin: '',
          },

          all_groups: [],

          maxPanChars: v => v && v.length <= 6 + 1 || 'Input too long!',
          maxPinChars: v => v && v.length == 4 + 1 || 'Pin Wrong!',

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

      testShowDialog() {
        this.$q.dialog({
          title: 'Alert',
          message: 'Modern HTML5 front-end framework on steroids.'
        })
      },

        myFilter (rows, terms, cols, cellValue) {
          const lowerTerms = terms ? terms.toLowerCase() : ''
          return rows.filter(
            row => cols.some( function(col) {
              return (JSON.stringify(cellValue(col,row)) + '').toLowerCase().indexOf(lowerTerms) !== -1;
            })
          )
        },

        customSort (rows, sortBy, descending) {
          let data = [...rows]

          if (sortBy) {
            data.sort((a, b) => {
              let x = descending ? b : a
              let y = descending ? a : b

              switch (sortBy) {
                case 'name':
                  // String Sort
                  return x[sortBy] > y[sortBy] ? 1 : x[sortBy] < y[sortBy] ? -1 : 0
                  break;

                case 'pin':
                case 'pan':
                  // String Sort
                  return x.pan[sortBy] > y.pan[sortBy] ? 1 : x.pan[sortBy] < y.pan[sortBy] ? -1 : 0
                  break;

                default:
                  // numeric sort
                  return parseFloat(x[sortBy]) - parseFloat(y[sortBy])
                  break;
              }

            })
          }

          return data;
        },

        getGroupPivotColor(group) {
            var p = group.pivot;
            if(p.is_mod && p.is_member) {
                return 'red';
            }
            if(p.is_mod) {
                return 'blue';
            }
            if(p.is_member) {
                return '';
            }
            return 'grey';
        },

        filterBasic(sSearch, sWhere) {
            if (!sSearch) return true
            if( !this.bExtendedFilter ) return true;
            return sWhere.toLowerCase().includes(sSearch.toLowerCase())
        },

        filterPan(sSearch, sWhere) {
            if (!sSearch) return true
            if( !this.bExtendedFilter ) return true;
            return sWhere.toLowerCase().includes(sSearch.replace(' ', '').toLowerCase())
        },

        filterId(sSearch, sWhere) {
            if (!sSearch) return true
            if( !this.bExtendedFilter ) return true;
            return sWhere == sSearch;
        },

        companyChanged(item) {
            item.company = this.findById(this.user.companies, item.company_id);
        },

        departmentChanged(item) {
            item.department = this.findById(this.user.departments, item.department_id);
        },

        locationChanged(item) {
            item.location = this.findById(this.user.locations, item.location_id);
        },

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
            if(typeof obj != 'undefined') {
                var copy = JSON.parse(JSON.stringify(obj));
                if(copy) return copy
            }
        },

        isSaved(item) {
            var key = this.getOldUsersId(item);
            var itemL = this.copyObject(item);
            var itemR = this.copyObject(this.usersCreatedOld[key]);

            if(itemL && itemR) {
                delete itemL.undefined;
                delete itemR.undefined;
                delete itemL.isSelected;
                delete itemR.isSelected;
                delete itemL.groupDialog;
                delete itemR.groupDialog;
                delete itemL.deleteUserDialog;
                delete itemR.deleteUserDialog;

                // Differences
                if(JSON.stringify(itemR) != JSON.stringify(itemL)) {
                    console.log(JSON.stringify(itemR), JSON.stringify(itemL));
                    return false;
                } else {
                    return true;
                }
            }

            return true;
        },

        isUnsaved(item) {
            return !this.isSaved(item);
        },

        getUnsaved(arr) {
            var unsaved = [];
            for (var i in arr) {
                var item = arr[i];

                if(this.isUnsaved(item)) {
                    unsaved.push(item);
                }
            }
            return unsaved;
        },


        resetUser(item) {
            var key = this.getOldUsersId(item);
            Object.assign(item, JSON.parse(JSON.stringify(this.usersCreatedOld[key])))
        },

        updateUser(user) {
            this.updateUsers([user]);
        },

        updateUsers(users) {
            // Variables
            var _this = this;
            this.loading = true;

            // Update Users
            this.$store.dispatch('users/updateUsers', {
                users: users
            }).then(function(e) {
                // Success
                if(!e || !e.response || !e.response.data || !e.response.data.error) {

                    _this.$q.notify({
                      message: this.$t('data_saved'),
                      color: 'green',
                      timeout: 3000,
                    })

                    // Save Old
                    for (var i in users) {
                        var user = users[i];
                        var key = _this.getOldUsersId(user);
                        Object.assign(_this.usersCreatedOld[key], JSON.parse(JSON.stringify(user)))
                    }
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

                _this.$q.notify({
                  message: _this.$t('attribute_unsaved') + "<br />" + errText,
                  color: 'error',
                  timeout: 6000,
                })

                _this.loading = false;
            });
        },

        findKeyById(arr, item) {
            return arr.findIndex(x => x.id === item.id);
        },

        findById(arr, id) {
            return arr.find(x => x.id === id);
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

                _this.$q.notify({
                  message: this.$t('data_saved'),
                  color: 'green',
                  timeout: 3000,
                })

                var tmpUsers = _this.copyObject(users);
                for (var i in tmpUsers) {
                    var tmpUser = tmpUsers[i];
                    _this.selected.splice( _this.findKeyById(_this.selected, tmpUser), 1);
                    _this.usersCreatedOld.splice( _this.findKeyById(_this.usersCreatedOld, tmpUser), 1);
                    _this.usersCreated.splice(_this.findKeyById(_this.usersCreated, tmpUser), 1);
                }

            });
        },

        save() {
            this.$q.notify({
              message: this.$t('attribute_changed'),
              color: 'primary',
              timeout: 3000,
            })
        },

        cancel () {
            this.$q.notify({
              message: this.$t('canceled'),
              color: 'primary',
              timeout: 3000,
            })
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

        addCreatedUserToGroup(user, group) {
            group.pivot = {
                is_mod : 0,
                is_member: 1,
            };

            user.groups.push(group);
        },

        removeCreatedUserFromGroup(user, index) {
            user.groups.splice(index, 1);
        },

    }

}

</script>

<style lang="sass">
.my-sticky-header-table
  /* max height is important */
  .q-table__middle
    max-height: 75vh

  thead tr th
    position: sticky
    z-index: 1
    background: #fff
  thead tr:first-child th
    top: 0

  /* this is when the loading indicator appears */
  &.q-table--loading thead tr:last-child th
    /* height of all previous header rows */
    top: 48px
</style>
