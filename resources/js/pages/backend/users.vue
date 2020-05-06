<template>
  <div>
    <h1>Users</h1>
    <p>Create Users with PAN & PIN</p>
    <p>
      <!-- Create User -->

      <q-btn
        label="Benutzer erstellen"
        unelevated
        color="primary"
        icon-right="mdi-account-plus"
        @click="bCreateUsersDialog = true"
      />
      <q-dialog v-model="bCreateUsersDialog" :maximized="maximizedToggle" max-width="700" persistent>
        <!-- Create User: Modal -->
        <q-card v-if="!bCreateUsersLoading">
          <q-toolbar class="bg-primary text-white" dark color="primary">
            <q-toolbar-title>Erstelle neue Zugänge</q-toolbar-title>

            <q-space />

            <q-btn dense flat icon="minimize" @click="maximizedToggle = false" :disable="!maximizedToggle">
              <q-tooltip v-if="maximizedToggle" content-class="bg-white text-primary">Minimieren</q-tooltip>
            </q-btn>
            <q-btn dense flat icon="crop_square" @click="maximizedToggle = true" :disable="maximizedToggle">
              <q-tooltip v-if="!maximizedToggle" content-class="bg-white text-primary">Maximieren</q-tooltip>
            </q-btn>
            <q-btn dense flat icon="close" v-close-popup>
              <q-tooltip content-class="bg-white text-primary">Schließen</q-tooltip>
            </q-btn>
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

            <q-item>
              <q-btn
                v-if="iCreateUsersNumber > 0"
                right
                dark
                color="primary"
                unelevated
                class="full-width q-mr-sm"
                :label="iCreateUsersNumber + ('Zugänge Generieren')"
                @click="createUsers()"
              />
            </q-item>
          </q-list>
          <q-separator />
        </q-card>
        <!-- Loading -->
        <template v-else>
          <p>{{ $t('loading.text') }}</p>
          <q-progress-linear indeterminate color="white" class="mb-0"></q-progress-linear>
        </template>
      </q-dialog>
      &nbsp;

      <q-btn
        label="Datei-Import"
        color="primary"
        unelevated
        icon-right="publish"
        @click="bImportUsersDialog = true"
      />
      <q-dialog v-model="bImportUsersDialog" maximized max-width="1200" persistent>
        <q-card>
          <q-toolbar class="bg-primary text-white" dark color="primary">
            <q-toolbar-title>Importieren</q-toolbar-title>
            <q-space />
            <q-btn dense flat icon="close" v-close-popup>
              <q-tooltip content-class="bg-white text-primary">Schließen</q-tooltip>
            </q-btn>
          </q-toolbar>
          <q-list three-line subheader>
            <q-item>
              <q-item-section>
                <q-item-label>Vorlage / Testdatei</q-item-label>
                <q-item-label caption>
                  <p>Bauen Sie die Datei wenn möglich anhand der Vorlage-Datei auf:</p>
                  <q-btn size="md" label="Testdatei herunterladen" depressed tile outline icon="get_app" />
                </q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />

            <q-item>
              <q-item-section>
                <q-item-label>Datei hochladen</q-item-label>
                <q-item-label caption>
                  Wählen Sie ihre Datei aus und beginnen Sie den Upload<br><br>
                  <q-uploader
                    url="http://localhost:4444/upload"
                    label="Datei auswählen und Hochladen"
                    color="primary"
                    square
                    flat
                    bordered
                    style="max-width: 300px;"
                    no-thumbnails
                  />
                </q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />

            <q-item v-if="false">
              <q-item-section>
                <q-item-label>Import Starten</q-item-label>
                <q-item-label caption>
                  Starten Sie den Import<br><br>
                </q-item-label>
              </q-item-section>
            </q-item>

          </q-list>
        </q-card>
      </q-dialog>
      &nbsp;
    </p>

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
          <!-- <div class="row" no-gutters align-content="end" align="end" justify="end">
                        <q-col cols="12" md="6">
                            <q-btn depressed color="primary">Benutzer erstellen</q-btn>
                        </q-col>
                        <q-col cols="12" md="6">
                            <q-input v-model="search" append-icon="search" label="Search" single-line hide-details />
                        </q-col>
                    </div> -->
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
          :virtual-scroll="usersCreated.length > 150"

          show-select
          :loading="loading"
          :loading-text="$t('loading.text')"
          :items-per-page="parseInt(itemsPerPage)"
          :footer-props="{
            showFirstLastPage: true,
          }"
          dense
          :selected.sync="selected"
          class="my-data-table"
        >

          <!-- Toolbars -->
          <template v-slot:top="props">

            <q-toolbar
              :class="'coha--toolbar ' + ((selected.length <= 0) ? '': 'bg-primary text-white')"
              floating
              min-height="85px"
              height="auto"
              :color="selected.length <= 0 ? '' : 'primary'"
              :dark="selected.length <= 0"
            >

              <!-- Always Visible -->

              <q-btn label="Ansicht" unelevated rounded icon="settings" dense>
                <q-menu>
                  <q-list style="min-width: 100px">
                    <q-item clickable>
                      <q-toggle v-model="showPin" label="PIN Anzeigen" size="sm" right-label color="red" style="width: 100%;" />
                    </q-item>
                    <q-separator />
                    <q-item clickable v-close-popup>
                      <q-toggle v-model="showContactMailData" label="Kontakt E-Mail Daten Anzeigen" size="sm" right-label color="red" style="width: 100%;" />
                    </q-item>
                  </q-list>
                </q-menu>
              </q-btn>&nbsp;

              <!-- No Select Toolbar -->
              <template v-if="selected.length <= 0"  :flat="search == ''">
                <!-- <q-toggle class="mt-6 mr-6" v-model="bExtendedFilter" :label="'Erweitert Filtern'" color="primary" /> -->
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
              </template>

              <!-- Toolbar for Selections -->
              <template v-else>

                <q-btn
                  :color="getUnsaved(selected).length ? 'green' : 'primary'"
                  rounded
                  unelevated
                  @click="updateUsers(getUnsaved(selected))"
                  :disabled="!getUnsaved(selected).length"
                  icon="save"
                  :label="getUnsaved(selected).length + ' ' + $t('save')"
                /> &nbsp;

                <q-btn
                  color="primary"
                  :label="selected.length + ' ' + $t('edit')"
                  icon="edit"
                  unelevated
                  rounded
                >
                  <q-menu>
                    <q-list style="min-width: 100px">

                      <BulkGroupChanges
                        :aItems="user.groups_moderating"
                        :selected="selected"
                      />

                      <!-- Menu: Company -->
                      <BulkProfileChanges
                        :aItems="user.companies"
                        :selected="selected"
                        sId="company_id"
                        sModel="company"
                        title="Ändere Firma"
                        label="Firma wählen"
                        menuText="Firma"
                      />

                      <!-- Menu: Department -->
                      <BulkProfileChanges
                        :aItems="user.departments"
                        :selected="selected"
                        sId="department_id"
                        sModel="department"
                        title="Ändere Abteilung"
                        label="Abteilung wählen"
                        menuText="Abteilung"
                      />

                      <!-- Menu: Location -->
                      <BulkProfileChanges
                        :aItems="user.locations"
                        :selected="selected"
                        sId="location_id"
                        sModel="location"
                        title="Ändere Ort"
                        label="Ort wählen"
                        menuText="Ort"
                      />
                    </q-list>
                  </q-menu>
                </q-btn>

                            &nbsp;

                <Print :users="selected" :disabled="getUnsaved(selected).length > 0 ? true : false" />

                <q-btn
                  rounded
                  unelevated
                  error
                  warning
                  @click.stop="deleteUsersDialog = true"
                  icon-left="delete"
                  :label="selected.length + ' ' + $t('delete')"
                />

                <q-dialog v-model="deleteUsersDialog" max-width="320" dark>
                  <q-card>
                    <q-toolbar class="bg-primary text-white" dark color="primary">
                      <q-toolbar-title>Zugänge Löschen</q-toolbar-title>
                      <q-space />
                      <q-btn dense flat icon="close" v-close-popup>
                        <q-tooltip content-class="bg-white text-primary">Schließen</q-tooltip>
                      </q-btn>
                    </q-toolbar>

                    <q-list>
                      <q-item>
                        <q-item-section>
                          <q-item-label>Benutzer Löschen</q-item-label>
                          <q-item-label caption>
                            Lösche die markierten Benutzer
                          </q-item-label>
                        </q-item-section>
                      </q-item>
                      <q-item>
                        <div class="container" fluid>
                          <div class="row" align="center">
                            <div class="text-center col-12 col-sm-12">
                              <q-btn unelevated @click.stop="deleteUsersDialog = false" outline>Abbruch</q-btn>
                              <q-btn unelevated @click="deleteUsers(selected)" color="red">Zugänge Löschen!</q-btn>
                            </div>
                          </div>
                        </div>
                      </q-item>
                    </q-list>
                  </q-card>
                </q-dialog>


                <!-- <q-checkbox left-label  class="mt-6 ml-6" v-model="bExtendedFilter" :label="'Erweitert Filtern'" color="accent"></q-checkbox> -->

                <q-toolbar-title></q-toolbar-title>
                <div class="flex-grow-1"></div>
                <q-input outlined dark v-model="itemsPerPage" number type="number" hide-details style="max-width: 150px;" label="Zeilen pro Seite" class="ml-5" outline  />
                <q-btn
                  flat round dense
                  :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                  @click="props.toggleFullscreen"
                  class="q-ml-md"
                />

              </template>

            </q-toolbar>

            <div class="container" fluid v-if="bExtendedFilter && false">
              <div class="row">
                <q-col>
                  <q-checkbox v-model="bShowDeletedUsers" color="primary" label="Gelöschte Nutzer zeigen"></q-checkbox>
                </q-col>
              </div>
            </div>
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
                  label-cancel="Schließen"
                  label-set="Übernehmen"
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
                  label-cancel="Schließen"
                  label-set="Übernehmen"
                >
                  <span class="coha--list-item pin c-code-text">
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
                      :class="showPin ? 'visible': 'secured'"
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
            <q-td :props="props" style="white-space: inherit;">
              <template v-if="user.groups_moderating && user.groups_moderating.length >= 1">
                <template v-for="(group, i) in props.row.groups">
                  <span v-bind:key="i">


                    <q-badge v-if="user.groups_moderating.find(x => x.id === group.id)" outlined class="mr-1 mt-1 mb-1"
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
                    </q-badge>
                    <q-badge v-else outlined disabled  class="mr-1 mt-1 mb-1">
                      {{ group.name }}
                      <q-tooltip bottom :open-delay="group.pivot ? group.pivot.is_member ? 1000 : 100 : 100">
                        Keine Rolle!
                      </q-tooltip>
                    </q-badge>

                  </span>
                </template>

                <!-- Gruppen hinzufügen / entfernen -->
                <template>

                  <q-popup-edit content-class="maximize" persistent buttons label-set="Übernehmen" label-cancel="Schließen" v-model="props.row">
                    <div class="inner">
                      <div class="text-h6">Gruppen hinzufügen / entfernen</div>
                      <q-separator />

                      <p>Der Gewählte Nutzer mit der ID "{{ props.row.id }}" und mit der PAN "{{ props.row.pan && props.row.pan.pan ? props.row.pan.pan : '' }}"</p>

                      <q-list subheader two-line flat>
                        <div>Nutzer ist in Gruppen:</div>
                        <template v-if="props.row.groups">
                          <template v-for="(group, i) in props.row.groups">
                            <div v-bind:key="group.id">
                              <q-item>
                                <q-item-section top avatar>
                                  <q-avatar color="primary" text-color="white" icon="mdi-account-multiple" />
                                </q-item-section>

                                <q-item-section>
                                  <q-item-label>{{ group.name }}</q-item-label>
                                  <q-item-label caption lines="2">
                                    {{ group.description_public || 'Ohne Gruppenbeschreibung' }}

                                    <p v-if="group.pivot">
                                      <br>
                                      <q-checkbox v-model="group.pivot.is_member" color="primary" dense hide-details label="Teilnehmer" :true-value="1" :false-value="0"></q-checkbox>
                                      <q-checkbox v-model="group.pivot.is_mod" color="red" dense hide-details label="Moderator" :true-value="1" :false-value="0"></q-checkbox>
                                    </p>

                                  </q-item-label>
                                </q-item-section>

                                <q-item-section side top>
                                  <q-item-label caption>
                                    ID #{{ group.id }}
                                  </q-item-label>
                                  <q-item-label>
                                    <q-btn
                                      unelevated
                                      outline
                                      text
                                      size="small"
                                      color="error"
                                      @click="removeCreatedUserFromGroup(props.row, i)"
                                      :label="$t('remove')"
                                    />
                                  </q-item-label>

                                </q-item-section>
                              </q-item>
                              <q-separator v-if="i+1 < props.row.groups.length" inset />
                            </div>
                          </template>
                        </template>
                      </q-list>

                      <br>


                      <q-list subheader two-line flat>
                        <div>Nutzer für diese Gruppen hinzufügen</div>
                        <template v-if="user.groups_moderating">
                          <template v-for="(group, i) in user.groups_moderating">
                            <div v-bind:key="group.id">
                              <q-item :disabled="alreadyInGroups(group, props.row.groups)">
                                <q-item-section top avatar>
                                  <q-avatar color="primary" text-color="white" icon="mdi-account-multiple-plus" />
                                </q-item-section>

                                <q-item-section>
                                  <q-item-label>{{ group.name }}</q-item-label>
                                  <q-item-label caption lines="2">
                                    <p>
                                      {{ group.description_public || 'Ohne Gruppenbeschreibung'  }}
                                    </p>

                                  </q-item-label>
                                </q-item-section>

                                <q-item-section side top>
                                  <q-item-label caption>
                                    ID #{{ group.id }}
                                  </q-item-label>
                                  <q-item-label>
                                    <q-btn
                                      v-if="alreadyInGroups(group, props.row.groups)"
                                      unelevated
                                      outlined
                                      text
                                      :label="$t('added')"
                                      size="small"
                                    />
                                    <q-btn
                                      v-else
                                      unelevated
                                      outlined
                                      text
                                      size="small"
                                      color="green"
                                      :label="$t('add_to')"
                                      @click="addCreatedUserToGroup(props.row, group)"
                                    />
                                  </q-item-label>

                                </q-item-section>
                              </q-item>
                              <q-separator v-if="i+1 < user.groups_moderating.length" inset />
                            </div>
                          </template>
                        </template>
                      </q-list>
                    </div>
                  </q-popup-edit>

                </template>

              </template>
            </q-td>
          </template>

          <!-- Company -->
          <template v-slot:body-cell-company="props">
            <q-td :props="props">
              <select v-model="props.row.company_id" @change="companyChanged(props.row)">
                <option disabled>Bitte auswählen</option>
                <option
                  :value="company.id"
                  :selected="(props.row.company && props.row.company.id == company.id)"
                  v-for="company in user.companies"
                  v-bind:key="company.id"
                >{{ company.name }}</option>
              </select>
            </q-td>
          </template>

          <!-- Department -->
          <template v-slot:body-cell-department="props">
            <q-td :props="props">
              <select v-model="props.row.department_id" @change="departmentChanged(props.row)">
                <option disabled>Bitte auswählen</option>
                <option
                  :value="department.id"
                  :selected="(props.row.department && props.row.department.id == department.id)"
                  v-for="department in user.departments"
                  v-bind:key="department.id"
                >{{ department.name }}</option>
              </select>
            </q-td>
          </template>

          <!-- Location -->
          <template v-slot:body-cell-location="props">
            <q-td :props="props">
              <select v-model="props.row.location_id" @change="locationChanged(props.row)">
                <option disabled>Bitte auswählen</option>
                <option
                  :value="location.id"
                  :selected="(props.row.location && props.row.location.id == location.id)"
                  v-for="location in user.locations"
                  v-bind:key="location.id"
                >{{ location.name }}</option>
              </select>
            </q-td>
          </template>

          <!-- Action Buttons -->
          <template v-slot:body-cell-action="props">
            <q-td :props="props">
              <div style="white-space: nowrap;">
                <span>
                  <q-btn size="sm" :color="isSaved(props.row) ? 'grey' : 'green'" flat round dense class="mr-2" @click="updateUser(props.row)" :disabled="isSaved(props.row) || !validUser(props.row)" icon="save" />
                  <q-tooltip>{{ $t('save') }}</q-tooltip>
                </span>

                <span>
                  <q-btn flat round dense icon="replay" :disabled="isSaved(props.row)" size="sm" class="mr-2" @click="resetUser(props.row)" />
                  <q-tooltip>{{ $t('reset') }}</q-tooltip>
                </span>

                <!-- <q-dialog v-model="props.row.deleteUserDialog" max-width="290" dark content-class="naked dark centered" transition="dialog-bottom-transition">
                                    <template v-slot:activator="{ on }">
                                        <v-icon small v-on="on">delete</v-icon>
                                    </template>
                                    <h2 class="display-2">Delete?</h2>
                                    <p>Delete?</p>
                                    <q-container fluid>
                                        <div class="row" align="center">
                                            <q-col class="text-center" cols="12" sm="12">
                                                <q-btn depressed @click.stop="props.row.deleteUserDialog = false" outlined>Abbruch</q-btn>
                                                <q-btn depressed @click="deleteUsers([props.row])" color="error">Zugang Löschen!</q-btn>
                                            </q-col>
                                        </div>
                                    </q-container>
                            </q-dialog> -->

              </div>
            </q-td>
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
      usersCreated: 'users/usersCreated'
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
          sortable: true
        },
        {
          label: this.$t('PAN'),
          name: 'pan',
          field: 'pan',
          filter: sWhere => {
            return this.filterPan(this.oFilters.sPan, sWhere)
          },
          sortable: true
        },
        {
          label: this.$t('PIN'),
          name: 'pin',
          field: 'pin',
          filter: sWhere => {
            return this.filterBasic(this.oFilters.sPin, sWhere)
          },
          sortable: true
        },
        {
          label: this.$t('groups'),
          name: 'groups',
          field: 'groups',
          sortable: true
        },
        {
          label: this.$t('company'),
          name: 'company',
          field: 'company',
          sortable: true
        },
        {
          label: this.$t('department'),
          name: 'department',
          field: 'department',
          sortable: true
        },
        {
          label: this.$t('location'),
          name: 'location',
          field: 'location',
          sortable: true
        },
        {
          label: this.$t('updated_at'),
          name: 'updated_at',
          field: 'updated_at',
          sortable: true
        },
        {
          label: this.$t('created_at'),
          name: 'created_at',
          field: 'created_at',
          sortable: true
        },
        {
          label: 'Aktionen',
          name: 'action',
          field: 'action',
          sortable: true
        }
      ];
    }
  },

  directives: {
    mask
  },

  mounted () {
    // Mounted
  },

  data () {
    return {
      pinLength: 4,
      panLength: 6,
      panTokens: {
        P: {
          pattern: /[0-9A-Za-z]/
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
        sPin: ''
      },

      all_groups: [],

      maxPanChars: v => v && v.length <= 6 | 'Input too long!',
      maxPinChars: v => v && v.length === 4 | 'Pin Wrong!',

      // Create Users
      iCreateUsersNumber: 5,
      bCreateUsersDialog: false,
      bCreateUsersLoading: false,
      bCreateUsersRandomPan: true,
      bCreateUsersRandomPin: true,
      maximizedToggle: false,

      deleteUsersDialog: false,

      // Import Users
      bImportUsersDialog: false,

      // Contact Mail Data
      showContactMailData: false

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
          let x  = descending ? b : a
          let xf = x[sortBy]
          let y  = descending ? a : b
          let yf = y[sortBy]

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

            case 'company':
            case 'location':
            case 'department':
              var xn = xf && xf.name ? xf.name : ''
              var yn = yf && yf.name ? yf.name : ''

              return xn > yn ? 1 : xn < yn ? -1 : 0
              break;

            case 'groups':
              var xid = xf.length > 0 ? xf.reduce(function(prev, curr) {
                return prev.id > curr.id ? prev : curr;
              }).id : 0

              var yid = yf.length > 0 ? yf.reduce(function(prev, curr) {
                return prev.id > curr.id ? prev : curr;
              }).id : 0

              return xid > yid ? 1 : xid < yid ? -1 : 0
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
          // console.log(JSON.stringify(itemR), JSON.stringify(itemL));
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
          console.log('success!!');

          _this.$q.notify({
            message: _this.$t('data_saved'),
            color: 'green',
            position: 'top',
            timeout: 3000
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
        if(!e || !e.response || !e.response.data) return;
        var res = e.response.data;
        var err = res.error;
        var errText = '';
        for (var e in err) {
          errText += ': ' + err[e];
        }

        _this.$q.notify({
          message: _this.$t('attribute_unsaved') + "<br />" + errText,
          color: 'error',
          timeout: 6000
        })

        _this.loading = false;
      });
    },

    findKeyById(arr, item) {
      return arr.findIndex(x => x.id === item.id);
    },

    findById (arr, id) {
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
          message: _this.$t('data_saved'),
          color: 'green',
          timeout: 3000,
          position: 'top'
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
        timeout: 3000
      })
    },

    cancel () {
      this.$q.notify({
        message: this.$t('canceled'),
        color: 'primary',
        timeout: 3000
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
        is_member: 1
      };

      user.groups.push(group);
    },

    removeCreatedUserFromGroup(user, index) {
      user.groups.splice(index, 1);
    }

  }

}

</script>

<style lang="scss">

.q-table__container {
  .q-table__top {
    padding: 0;
    .q-toolbar {
      padding: 15px;
    }
  }
}

</style>
