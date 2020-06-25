<template>
  <div>
    <h1>Zugänge verwalten</h1>
    <p>Erstelle Benutzerkonten und dafür jeweils PAN & PIN</p>
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

            <q-btn dense flat icon="minimize" :disable="!maximizedToggle" @click="maximizedToggle = false">
              <q-tooltip v-if="maximizedToggle" content-class="bg-white text-primary">
                Minimieren
              </q-tooltip>
            </q-btn>
            <q-btn dense flat icon="crop_square" :disable="maximizedToggle" @click="maximizedToggle = true">
              <q-tooltip v-if="!maximizedToggle" content-class="bg-white text-primary">
                Maximieren
              </q-tooltip>
            </q-btn>
            <q-btn v-close-popup dense flat icon="close">
              <q-tooltip content-class="bg-white text-primary">
                Schließen
              </q-tooltip>
            </q-btn>
          </q-toolbar>
          <q-list three-line subheader>
            <q-item>
              <q-item-section>
                <q-item-label>Benutzer / Zugänge erstellen</q-item-label>
                <q-item-label caption>
                  Wählen Sie eine Anzahl der zu erstellenden Benutzer aus
                </q-item-label>
              </q-item-section>
            </q-item>

            <q-item>
              <q-item-section style="max-width:400px;">
                <q-input v-model="iCreateUsersNumber" outlined clearable label="Benutzeranzahl" required hide-details type="number" autofocus />
              </q-item-section>
            </q-item>

            <q-item v-ripple tag="label">
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
                :label="iCreateUsersNumber + (' Zugänge Generieren')"
                @click="createUsers(iCreateUsersNumber)"
              />
            </q-item>
          </q-list>
          <q-separator />
        </q-card>
        <!-- Loading -->
        <template v-else>
          <p class="text-white">
            {{ $t('loading.text') }}
          </p>
          <q-progress-linear indeterminate color="white" class="mb-0" />
        </template>
      </q-dialog>
      &nbsp;

      <CsvUsersImport ref="CsvUsersImport" />

      &nbsp;
    </p>

    <p>
      <UserDataModal
        s-icon="group"
        :b-outline="false"
        s-edit-text="Gruppen Bearbeiten"
        s-create-text="Neue Gruppe erstellen"
        s-input-label="Gruppenname"
        s-input-label2="Gruppenbeschreibung Öffentlich"
        s-input-label3="Gruppenbeschreibung für Moderatoren"
        s-parent-model="group"
        :a-parent-models="user.groups_moderating"
        :a-parent-headers="[
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
        ]"
      />

      <UserDataModal
        s-icon="apartment"
        s-edit-text="Firmen bearbeiten"
        s-create-text="Neue Firma erstellen"
        s-input-label="Firmenname"
        s-parent-model="company"
        :a-parent-models="user.companies"
      />

      <UserDataModal
        s-icon="work"
        s-edit-text="Abteilungen bearbeiten"
        s-create-text="Neue Abteilung Erstellen"
        s-input-label="Abteilungsname"
        s-parent-model="department"
        :a-parent-models="user.departments"
      />

      <UserDataModal
        s-icon="location_on"
        s-edit-text="Orte Bearbeiten"
        s-create-text="Neuen Ort erstellen"
        s-input-label="Ortsname"
        s-parent-model="location"
        :a-parent-models="user.locations"
      />
    </p>

    <br>

    <template>
      <q-card>
        <q-card-section>
          <div class="text-h6">
            Erstellte Benutzer
          </div>
          <div class="text-subtitle2">
            Alle erstellten Benutzer
          </div>
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
          :pagination.sync="pagination"

          :loading="loading"
          :loading-text="$t('loading.text')"
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
                <q-menu desne>
                  <q-list dense style="min-width: 100px">
                    <q-item clickable :lowlighted="!bShowPin">
                      <q-item-section avatar>
                        <q-icon name="no_encryption" :color="bShowPin ? 'red' :''" />
                      </q-item-section>
                      <q-checkbox v-model="bShowPin" label="PIN Anzeigen" size="sm" left-label />
                    </q-item>
                    <q-item clickable :lowlighted="!settings.bShowDates">
                      <q-item-section avatar>
                        <q-icon name="date_range" />
                      </q-item-section>
                      <q-checkbox v-model="settings.bShowDates" label="Datumsfelder Anzeigen" size="sm" color="primary" left-label />
                    </q-item>
                    <q-separator />
                    <q-item clickable :lowlighted="!settings.bShowContactMailData">
                      <q-item-section avatar>
                        <q-icon name="mail" />
                      </q-item-section>
                      <q-checkbox v-model="settings.bShowContactMailData" label="Kontakt / E-Mail Versand-Daten Anzeigen" size="sm" left-label />
                    </q-item>
                    <q-item clickable :lowlighted="!settings.bShowImportComment">
                      <q-item-section avatar>
                        <q-icon name="comment" />
                      </q-item-section>
                      <q-checkbox v-model="settings.bShowImportComment" label="Import-Kommentar Anzeigen" size="sm" left-label />
                    </q-item>
                    <q-separator />
                  </q-list>
                </q-menu>
              </q-btn>&nbsp;

              <!-- No Select Toolbar -->
              <template v-if="selected.length <= 0" :flat="search == ''">
                <!-- <q-toggle class="mt-6 mr-6" v-model="bExtendedFilter" :label="'Erweitert Filtern'" color="primary" /> -->
                <div class="flex-grow-1" />
                <q-toolbar-title />
                <q-input
                  v-model="search"
                  outlined
                  style="max-width: 400px;"
                  :label="$t('Search')"
                  autocomplete="off"
                  append-icon="search"
                  hide-details
                  outline
                />

                &nbsp;

                <q-input v-model="pagination.rowsPerPage" outlined number type="number" hide-details style="max-width: 150px;" label="Zeilen pro Seite" class="ml-5" outline />
                <q-btn
                  flat round dense
                  :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                  class="q-ml-md"
                  @click="props.toggleFullscreen"
                />
              </template>

              <!-- Toolbar for Selections -->
              <template v-else>
                <q-btn
                  :color="getUnsaved(selected).length ? 'green' : 'primary'"
                  rounded
                  unelevated
                  :disabled="!getUnsaved(selected).length"
                  icon="save"
                  :label="getUnsaved(selected).length + ' ' + $t('save')"
                  @click="updateUsers(getUnsaved(selected))"
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
                        :a-items="user.groups_moderating"
                        :selected="selected"
                      />

                      <!-- Menu: Company -->
                      <BulkProfileChanges
                        :a-items="user.companies"
                        :selected="selected"
                        s-id="company_id"
                        s-model="company"
                        title="Ändere Firma"
                        label="Firma wählen"
                        menu-text="Firma"
                      />

                      <!-- Menu: Department -->
                      <BulkProfileChanges
                        :a-items="user.departments"
                        :selected="selected"
                        s-id="department_id"
                        s-model="department"
                        title="Ändere Abteilung"
                        label="Abteilung wählen"
                        menu-text="Abteilung"
                      />

                      <!-- Menu: Location -->
                      <BulkProfileChanges
                        :a-items="user.locations"
                        :selected="selected"
                        s-id="location_id"
                        s-model="location"
                        title="Ändere Ort"
                        label="Ort wählen"
                        menu-text="Ort"
                      />

                      <!-- Bulk Sending Mails -->

                      <!-- Bulk Delete -->
                      <q-separator />

                      <q-item clickable>
                        <q-item-section>Entfernen</q-item-section>
                        <q-item-section side>
                          <q-icon name="keyboard_arrow_right" />
                        </q-item-section>
                        <q-menu anchor="top right" self="top left">
                          <q-list>
                            <q-item clickable @click.stop="deleteUsersDialog = true">
                              <q-item-section>{{ selected.length + ' ' + $t('delete') }}</q-item-section>
                              <q-item-section side>
                                <q-icon name="delete" />
                              </q-item-section>
                            </q-item>
                          </q-list>
                        </q-menu>
                      </q-item>

                      <!-- Delete Dialog -->
                      <q-dialog v-model="deleteUsersDialog" max-width="320" dark>
                        <q-card>
                          <q-toolbar class="bg-primary text-white" dark color="primary">
                            <q-toolbar-title>Zugänge Löschen</q-toolbar-title>
                            <q-space />
                            <q-btn v-close-popup dense flat icon="close">
                              <q-tooltip content-class="bg-white text-primary">
                                Schließen
                              </q-tooltip>
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
                                    <q-btn unelevated outline @click.stop="deleteUsersDialog = false">
                                      Abbruch
                                    </q-btn>
                                    <q-btn unelevated color="red" @click="deleteUsers(selected)">
                                      Zugänge Löschen!
                                    </q-btn>
                                  </div>
                                </div>
                              </div>
                            </q-item>
                          </q-list>
                        </q-card>
                      </q-dialog>
                    </q-list>
                  </q-menu>
                </q-btn>

                            &nbsp;
                <q-btn
                  color="primary"
                  :label="selected.length + ' ' + $t('teilen')"
                  icon="share"
                  unelevated
                  rounded
                  :disabled="getUnsaved(selected).length > 0 ? true : false"
                >
                  <q-menu>
                    <q-list style="min-width: 100px">
                      <Print :users="selected" />

                      <q-separator />

                      <q-item clickable @click="trySendEntranceMails()">
                        <q-item-section>
                          Zugangs-E-Mails versenden
                        </q-item-section>
                        <q-item-section side>
                          <q-icon name="email" />
                        </q-item-section>
                      </q-item>

                      <q-dialog v-model="entranceMail.dialog" max-width="770px">
                        <q-card>
                          <q-card-section>
                            <div class="text-h6">
                              Send multiple Entrance-Mails
                            </div>
                          </q-card-section>

                          <q-card-section class="q-pt-none">
                            {{ 'Try send Entrance-Mail for ' + (selected.length) + ' Users' }}
                          </q-card-section>

                          <q-card-section class="q-pt-none">
                            <q-input
                              v-model="entranceMail.subject"
                              filled
                              label="E-Mail Betreff"
                              hint="Subject / E-Mail Betreff"
                              placeholder="z.B.: 'Herzlich Willkommen liebe Umfrage-Teilnehmer!' ..."
                            />
                            <br>
                            <q-input
                              v-model="entranceMail.text"
                              filled
                              clearable
                              type="textarea"
                              label="E-Mail Text"
                              hint="Zusätzlicher Text. In der E-Mail. Oberhalb der Zugangsdaten"
                              placeholder="(Optionaler Text) z.B.: 'Im Auftrag der Firma-XYZ versenden wir Ihnen ...'"
                            />
                            <br>
                            <q-input
                              v-model="entranceMail.signature"
                              filled
                              label="E-Mail Signatur"
                              hint="Signature / E-Mail Signatur. Innerhalb der E-Mail. Unterhalb der Zugangsdaten"
                              placeholder="(Optionaler Text) z.B.: 'Gekennzeichnet, Author-Name / Firma'"
                            />
                            <br>
                            <q-checkbox
                              v-model="entranceMail.checkbox"
                              label="Ich möchte einen generierten Zugang einem oder mehreren Befragungs-Teilnehmer via E-Mail zusenden. Mir ist bewusst dass ich versendete E-Mails nicht löschen oder rückgängig machen kann."
                            />
                            <br>
                          </q-card-section>

                          <q-card-actions align="right">
                            <q-btn v-close-popup label="Abbruch" unelevated color="grey" />
                            <q-space />
                            <q-btn v-close-popup :disable="!entranceMail.checkbox" label="E-Mails versenden" unelevated color="red" @click="sendEntranceMails()" />
                          </q-card-actions>
                        </q-card>
                      </q-dialog>
                      <q-separator />
                    </q-list>
                  </q-menu>
                </q-btn>

                <!-- <q-checkbox left-label  class="mt-6 ml-6" v-model="bExtendedFilter" :label="'Erweitert Filtern'" color="accent"></q-checkbox> -->

                <q-toolbar-title />
                <div class="flex-grow-1" />
                <q-input v-model="pagination.rowsPerPage" outlined dark number type="number" hide-details style="max-width: 150px;" label="Zeilen pro Seite" class="ml-5" outline />
                <q-btn
                  flat
                  round
                  dense
                  :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                  class="q-ml-md"
                  @click="props.toggleFullscreen"
                />
              </template>
            </q-toolbar>

            <div v-if="bExtendedFilter && false" class="container" fluid>
              <div class="row">
                <q-col>
                  <q-checkbox v-model="bShowDeletedUsers" color="primary" label="Gelöschte Nutzer zeigen" />
                </q-col>
              </div>
            </div>
          </template>

          <!-- Header Cell Slots -->
          <template v-slot:header-cell-import_comment="props">
            <q-th v-if="settings.bShowImportComment" :props="props">
              {{ props.col.label }}
            </q-th>
          </template>

          <!-- Header Cell Slots -->
          <template v-slot:header-cell-contact_mail="props">
            <q-th v-if="settings.bShowContactMailData" :props="props">
              {{ props.col.label }}
            </q-th>
          </template>

          <!-- Header Cell Slots -->
          <template v-slot:header-cell-last_mail_date="props">
            <q-th v-if="settings.bShowContactMailData" :props="props">
              {{ props.col.label }}
            </q-th>
          </template>

          <!-- Header Cell Slots -->
          <template v-slot:header-cell-last_mail_status="props">
            <q-th v-if="settings.bShowContactMailData" :props="props">
              {{ props.col.label }}
            </q-th>
          </template>

          <!-- Date: Header Cell Slots -->
          <template v-slot:header-cell-updated_at="props">
            <q-th v-if="settings.bShowDates" :props="props">
              {{ props.col.label }}
            </q-th>
          </template>
          <template v-slot:header-cell-created_at="props">
            <q-th v-if="settings.bShowDates" :props="props">
              {{ props.col.label }}
            </q-th>
          </template>

          <!-- PAN -->
          <template v-slot:body-cell-pan="props">
            <q-td :props="props">
              <div class="pan--dialog-input c-code-text">
                <template v-if="props && props.row && props.row.pan.pan && props.row.pan.pan.length > 0">
                  <span :class="!panIsOk(props.row) ? 'text-red' : ''">
                    <span class="pan--part">{{
                      props.row.pan.pan.substring(0,3)
                    }}</span><span class="pan--part">{{
                      props.row.pan.pan.substring(3,6)
                    }}</span>
                  </span>
                </template>
                <template v-else>
                  <span style="text-transform: uppercase;" class="text-red c-code-text">{{ $t('empty') }}</span>
                </template>
                <q-popup-edit
                  v-model="props.row.pan.pan"
                  buttons
                  single-line
                  :label="$t('edit')"
                  persistent
                  :cover="false"
                  self="center left"
                  anchor="center right"
                  :offset="[5, 0]"
                  label-cancel="Schließen"
                  label-set="Übernehmen"
                  @save="save(props.row)"
                  @cancel="cancel"
                >
                  <span class="coha--list-item pan c-code-text ">
                    <q-input
                      v-model="props.row.pan.pan"
                      dense
                      autofocus
                      counter
                      persistent-hint
                      :rules="[maxPanChars]"
                      maxlength="6"
                      required
                      :error="!panIsOk(props.row)"
                      :disabled="panIsLoading"
                      :loading="panIsLoading"
                      @keydown="changePan(props.row)"
                      @keyup="changePan(props.row)"
                      @change="changePan(props.row)"
                    >
                      <template v-slot:append>
                        <q-btn dense flat round icon="repeat" @click="getRandomPan(props.row)" />
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
                  <span
                    :class="pinIsOk(props.row) ? 'text-grey' : 'text-red'"
                  >
                    {{ bShowPin ? props.row.pan.pin : '****' }}
                  </span>
                </template>
                <template v-else>
                  <span style="text-transform: uppercase;" class="text-red c-code-text">{{ $t('empty') }}</span>
                </template>

                <q-popup-edit
                  v-model="props.row.pan.pin"
                  buttons
                  single-line
                  :label="$t('edit')"
                  persistent
                  :cover="false"
                  self="center left"
                  anchor="center right"
                  :offset="[5, 0]"
                  label-cancel="Schließen"
                  label-set="Übernehmen"
                  @save="save(props.row)"
                  @cancel="cancel"
                >
                  <span class="coha--list-item pin c-code-text">
                    <q-input
                      v-model="props.row.pan.pin"
                      v-mask="'####'"
                      dense
                      autofocus
                      counter
                      persistent-hint
                      :rules="[maxPinChars]"
                      :error="!pinIsOk(props.row)"
                      required
                      autocomplete="off"
                      type="text"
                      pattern="[0-9]*"
                      name="pin"
                      maxlength="4"
                      :class="bShowPin ? 'visible': 'secured'"
                      @keyup="changePin(props.row)"
                      @change="changePin(props.row)"
                    >
                      <template v-slot:append>
                        <q-btn dense flat round icon="repeat" @click="generateRandomPin(props.row)" />
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
            <q-td class="groups" :props="props">
              <template v-if="user.groups_moderating && user.groups_moderating.length >= 1">
                <template v-for="(group, i) in props.row.groups">
                  <span :key="i">

                    <q-badge v-if="user.groups_moderating.find(x => x.id === group.id)" outlined class="mr-1 mt-1 mb-1"
                             :color="getGroupPivotColor(group)"
                    >
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
                    <q-badge v-else outlined disabled class="mr-1 mt-1 mb-1">
                      {{ group.name }}
                      <q-tooltip bottom :open-delay="group.pivot ? group.pivot.is_member ? 1000 : 100 : 100">
                        Keine Rolle!
                      </q-tooltip>
                    </q-badge>

                  </span>
                </template>

                <!-- Gruppen hinzufügen / entfernen -->
                <template>
                  <q-popup-edit v-model="props.row" content-class="maximize" persistent buttons label-set="Übernehmen" label-cancel="Schließen">
                    <div class="inner">
                      <div class="text-h6">
                        Gruppen hinzufügen / entfernen
                      </div>
                      <q-separator />

                      <p>Der Gewählte Nutzer mit der ID "{{ props.row.id }}" und mit der PAN "{{ props.row.pan && props.row.pan.pan ? props.row.pan.pan : '' }}"</p>

                      <q-list subheader two-line flat>
                        <div>Nutzer ist in Gruppen:</div>
                        <template v-if="props.row.groups">
                          <template v-for="(group, i) in props.row.groups">
                            <div :key="group.id">
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
                                      <q-checkbox v-model="group.pivot.is_member" color="primary" dense hide-details label="Teilnehmer" :true-value="1" :false-value="0" />
                                      <q-checkbox v-model="group.pivot.is_mod" color="red" dense hide-details label="Moderator" :true-value="1" :false-value="0" />
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
                                      :label="$t('remove')"
                                      @click="removeCreatedUserFromGroup(props.row, i)"
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
                            <div :key="group.id">
                              <q-item :disabled="alreadyInGroups(group, props.row.groups)">
                                <q-item-section top avatar>
                                  <q-avatar color="primary" text-color="white" icon="mdi-account-multiple-plus" />
                                </q-item-section>

                                <q-item-section>
                                  <q-item-label>{{ group.name }}</q-item-label>
                                  <q-item-label caption lines="2">
                                    <p>
                                      {{ group.description_public || 'Ohne Gruppenbeschreibung' }}
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
                <option disabled label="Bitte auswählen" />
                <option
                  v-for="company in user.companies"
                  :key="company.id"
                  :label="company.name"
                  :value="company.id"
                  :selected="(props.row.company && props.row.company.id == company.id)"
                />
              </select>
            </q-td>
          </template>

          <!-- Department -->
          <template v-slot:body-cell-department="props">
            <q-td :props="props">
              <select v-model="props.row.department_id" @change="departmentChanged(props.row)">
                <option disabled label="Bitte auswählen" />
                <option
                  v-for="department in user.departments"
                  :key="department.id"
                  :label="department.name"
                  :value="department.id"
                  :selected="(props.row.department && props.row.department.id == department.id)"
                />
              </select>
            </q-td>
          </template>

          <!-- Location -->
          <template v-slot:body-cell-location="props">
            <q-td :props="props">
              <select v-model="props.row.location_id" @change="locationChanged(props.row)">
                <option disabled label="Bitte auswählen" />
                <option
                  v-for="location in user.locations"
                  :key="location.id"
                  :label="location.name"
                  :value="location.id"
                  :selected="(props.row.location && props.row.location.id == location.id)"
                />
              </select>
            </q-td>
          </template>

          <!-- import_comment -->
          <template v-slot:body-cell-import_comment="props">
            <q-td v-if="settings.bShowImportComment" class="import_comment" :props="props">
              <template v-if="props.row.pan.import_comment">
                <span class="code_font">{{ props.row.pan.import_comment }}</span>
              </template>
            </q-td>
          </template>

          <!-- contact_mail -->
          <template v-slot:body-cell-contact_mail="props">
            <q-td v-if="settings.bShowContactMailData" :props="props">
              <div>
                <template v-if="props && props.row && props.row.pan.contact_mail">
                  <span class="code_font contact_mail">{{ props.row.pan.contact_mail }}</span>
                </template>
                <template v-else>
                  <span style="text-transform: uppercase;" class="text-grey">{{ $t('empty') }}</span>
                </template>
                <q-popup-edit
                  v-model="props.row.pan.contact_mail"
                  buttons
                  single-line
                  :label="$t('edit')"
                  persistent
                  :cover="false"
                  self="center left"
                  anchor="center right"
                  :offset="[5, 0]"
                  label-cancel="Schließen"
                  label-set="Übernehmen"
                  @save="save(props.row)"
                  @cancel="cancel"
                >
                  <span class="coha--list-item">
                    <q-input
                      v-model="props.row.pan.contact_mail"
                      dense
                      autofocus
                      persistent-hint
                      :rules="[val => !!val || 'Email is missing', isValidEmail]"
                      required
                      type="email"
                      :error="!panIsOk(props.row)"
                      :disabled="panIsLoading"
                      :loading="panIsLoading"
                    />
                  </span>
                </q-popup-edit>
              </div>
            </q-td>
          </template>

          <!-- last_mail_date -->
          <template v-slot:body-cell-last_mail_date="props">
            <q-td v-if="settings.bShowContactMailData" :props="props">
              <template v-if="props.row.pan.last_mail_date">
                <span class="code_font last_mail_date">{{ props.row.pan.last_mail_date }}</span><br>
                <span :class="getLastMailColorClass(props.row.pan.last_mail_date)">
                  {{ $t('time since') }}: <span>{{ timeSince(date(props.row.pan.last_mail_date)) }}</span>
                </span>
              </template>
            </q-td>
          </template>

          <!-- last_mail_status -->
          <template v-slot:body-cell-last_mail_status="props">
            <q-td v-if="settings.bShowContactMailData" :props="props">
              <span class="code_font last_mail_status">{{ props.row.pan.last_mail_status }}</span>
            </q-td>
          </template>

          <!-- Dates -->
          <template v-slot:body-cell-updated_at="props">
            <q-td v-if="settings.bShowDates" :props="props">
              {{ props.row.updated_at }}
            </q-td>
          </template>
          <template v-slot:body-cell-created_at="props">
            <q-td v-if="settings.bShowDates" :props="props">
              {{ props.row.created_at }}
            </q-td>
          </template>

          <!-- Action Buttons -->
          <template v-slot:body-cell-action="props">
            <q-td :props="props">
              <div style="white-space: nowrap;">
                <span v-if="isUnsaved(props.row) && validUser(props.row)">
                  <q-btn
                    size="sm"
                    flat
                    round
                    dense
                    class="mr-2"
                    icon="save"
                    :color="isSaved(props.row) ? 'grey' : 'green'"
                    :disabled="isSaved(props.row) || !validUser(props.row)"
                    @click="updateUser(props.row)"
                  />
                  <q-tooltip>{{ $t('save') }}</q-tooltip>
                </span>

                <!-- <span v-if="settings.bShowContactMailData && isSaved(props.row) && props.row.pan.contact_mail">
                  <q-btn
                    size="sm"
                    flat
                    round
                    dense
                    class="mr-2"
                    icon="mail"
                    color="primary"
                    :disabled="isUnsaved(props.row) || !validUser(props.row)"
                    @click="trySendEntranceMail(props.row)"
                  />
                  <q-tooltip>{{ $t('Versende Zugangs-Mail') }}</q-tooltip>
                </span> -->

                <span v-if="isUnsaved(props.row)">
                  <q-btn
                    flat
                    round
                    dense
                    icon="replay"
                    size="sm"
                    class="mr-2"
                    :disabled="isSaved(props.row)"
                    @click="resetUser(props.row)"
                  />
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
/* eslint-disable eqeqeq */
import axios from 'axios'
import { mapGetters } from 'vuex'
// import { setTimeout } from 'timers'
import { mask } from 'vue-the-mask'
import dayjs from 'dayjs'
import UserDataModal from '~/components/Backend/UserDataModal'
import Print from '~/components/Backend/UsersPrint'
import BulkProfileChanges from '~/components/Backend/UserBulkProfileChanges'
import BulkGroupChanges from '~/components/Backend/UserBulkGroupChanges'
import CsvUsersImport from '~/components/Backend/CsvUsersImport'
// import { type } from 'os'

export default {
  middleware: 'canCreateUsers',

  components: {
    UserDataModal,
    Print,
    BulkProfileChanges,
    BulkGroupChanges,
    CsvUsersImport
  },

  directives: {
    mask
  },

  data () {
    return {

      // Entrance Mails
      entranceMail: {
        dialog: false,
        subject: 'Herzlich Willkommen',
        text: '',
        signature: '',
        checkbox: false
      },

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

      // Settings / Filter
      bShowPin: false,
      settings: {
        bShowDates: false,
        bShowContactMailData: false, // Contact Mail Data
        bShowImportComment: false // Contact Mail Data
      },

      // Create Users
      iCreateUsersNumber: 5,
      bCreateUsersDialog: false,
      bCreateUsersLoading: false,
      bCreateUsersRandomPan: true,
      bCreateUsersRandomPin: true,
      maximizedToggle: false,

      // Delete Users
      deleteUsersDialog: false,

      // Import Users

      // Pagination
      pagination: {
        sortBy: 'id',
        descending: true,
        rowsPerPage: 10
      }

    }
  },

  mounted () {
    // Mounted
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      usersCreated: 'users/usersCreated'
    }),

    headers () {
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

        // Only if
        /*
          import_comment
          contact_mail
          last_mail_date
          last_mail_status
        */
        {
          label: this.$t('import comment'),
          name: 'import_comment',
          field: 'import_comment',
          sortable: true
        },
        {
          label: this.$t('contact mail'),
          name: 'contact_mail',
          field: 'contact_mail',
          sortable: true
        },
        {
          label: this.$t('last mail date'),
          name: 'last_mail_date',
          field: 'last_mail_date',
          sortable: true
        },
        {
          label: this.$t('last mail status'),
          name: 'last_mail_status',
          field: 'last_mail_status',
          sortable: true
        },

        // Dates
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
      ]
    }
  },

  watch: {
    usersCreated (promise) {
      // save Promise result in local state
      this.usersCreatedOld = JSON.parse(JSON.stringify(promise))
    },

    settings: {
      handler: function (newValue) {
        console.log('update settings now')
        this.updateSettings()
      },
      deep: true
    }
  },

  created: function () {
    this.$store.dispatch('users/fetchUsersCreated')
    this.$store.dispatch('users/fetchGroupsModerating')
    this.getSettings()
  },

  methods: {

    getLastMailColorClass (date) {
      let sec = this.secondsSince(date)

      switch (true) {
        case (sec < 3600):
          // Unter 1 stunde
          return 'text-red'

        case (sec < 86400):
          // Unter ein Tag
          return 'text-orange'

        case (sec < 604800):
          // Unter einer woche
          return 'text-yellow-8'

        default:
          return 'text-grey'
      }
    },

    secondsSince (date) {
      return Math.floor((this.date() - this.date(date)) / 1000)
    },

    timeSince (date) {
      let seconds = this.secondsSince(date)
      let interval = Math.floor(seconds / 31536000)

      if (interval > 1) {
        return interval + ' ' + this.$t('years')
      }
      interval = Math.floor(seconds / 2592000)
      if (interval > 1) {
        return interval + ' ' + this.$t('months')
      }
      interval = Math.floor(seconds / 86400)
      if (interval > 1) {
        return interval + ' ' + this.$t('days')
      }
      interval = Math.floor(seconds / 3600)
      if (interval > 1) {
        return interval + ' ' + this.$t('hours')
      }
      interval = Math.floor(seconds / 60)
      if (interval > 1) {
        return interval + ' ' + this.$t('minutes')
      }

      return Math.floor(seconds) + ' ' + this.$t('seconds')
    },

    date (d) {
      return dayjs(d)
    },

    isValidEmail (val) {
      const emailPattern = /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/
      return emailPattern.test(val) || 'Invalid email'
    },

    getSettings () {
      this.settings = JSON.parse(localStorage.getItem('settings')) ?? this.settings
      this.updateSettings()
    },

    updateSettings () {
      localStorage.setItem('settings', JSON.stringify(this.settings))
    },

    myFilter (rows, terms, cols, cellValue) {
      const lowerTerms = terms ? terms.toLowerCase() : ''
      return rows.filter(
        row => cols.some(function (col) {
          return (JSON.stringify(cellValue(col, row)) + '').toLowerCase().indexOf(lowerTerms) !== -1
        })
      )
    },

    customSort (rows, sortBy, descending) {
      let data = [...rows]

      if (sortBy) {
        data.sort((a, b) => {
          let x = descending ? b : a
          let xf = x[sortBy]
          let y = descending ? a : b
          let yf = y[sortBy]

          switch (sortBy) {
            case 'name':
              // String Sort
              return x[sortBy] > y[sortBy] ? 1 : x[sortBy] < y[sortBy] ? -1 : 0
              break

            case 'pin':
            case 'pan':
              // String Sort
              return x.pan[sortBy] > y.pan[sortBy] ? 1 : x.pan[sortBy] < y.pan[sortBy] ? -1 : 0
              break

            case 'company':
            case 'location':
            case 'department':
              var xn = xf && xf.name ? xf.name : ''
              var yn = yf && yf.name ? yf.name : ''

              return xn > yn ? 1 : xn < yn ? -1 : 0
              break

            case 'groups':
              var xid = xf.length > 0 ? xf.reduce(function (prev, curr) {
                return prev.id > curr.id ? prev : curr
              }).id : 0

              var yid = yf.length > 0 ? yf.reduce(function (prev, curr) {
                return prev.id > curr.id ? prev : curr
              }).id : 0

              return xid > yid ? 1 : xid < yid ? -1 : 0
              break

            default:
              // numeric sort
              return parseFloat(x[sortBy]) - parseFloat(y[sortBy])
              break
          }
        })
      }

      return data
    },

    getGroupPivotColor (group) {
      var p = group.pivot
      if (p.is_mod && p.is_member) {
        return 'red'
      }
      if (p.is_mod) {
        return 'blue'
      }
      if (p.is_member) {
        return ''
      }
      return 'grey'
    },

    filterBasic (sSearch, sWhere) {
      if (!sSearch) return true
      if (!this.bExtendedFilter) return true
      return sWhere.toLowerCase().includes(sSearch.toLowerCase())
    },

    filterPan (sSearch, sWhere) {
      if (!sSearch) return true
      if (!this.bExtendedFilter) return true
      return sWhere.toLowerCase().includes(sSearch.replace(' ', '').toLowerCase())
    },

    filterId (sSearch, sWhere) {
      if (!sSearch) return true
      if (!this.bExtendedFilter) return true
      return sWhere == sSearch
    },

    companyChanged (item) {
      item.company = this.findById(this.user.companies, item.company_id)
    },

    departmentChanged (item) {
      item.department = this.findById(this.user.departments, item.department_id)
    },

    locationChanged (item) {
      item.location = this.findById(this.user.locations, item.location_id)
    },

    getRandomPan (user) {
      var _this = this
      _this.panIsLoading = true

      axios.get('/api/get-random-pan')
        .then(function (response) {
          _this.panIsLoading = false
          user.pan.pan = response.data
        }).catch(function (response) {
          _this.panIsLoading = false
        })
    },

    generateRandomPin (item) {
      if (item && item.pan && item.pan.pin) {
        item.pan.pin = Math.random().toString().substr(2, this.pinLength)
      }
    },

    createUsers (number, imported) {
      var _this = this

      // Close Dialog and Load
      this.bCreateUsersLoading = true

      // Create Users
      this.$store.dispatch('users/createUsers', {
        number: number,
        imported: imported
      }).then(function (response) {
        _this.bCreateUsersLoading = false
        _this.bCreateUsersDialog = false

        // Success
        var users = response.data
        for (var i in users) {
          var oUser = users[i]
          oUser.isSelected = true

          // Add to Array
          _this.usersCreated.unshift(oUser)
          _this.selected.unshift(oUser)
        }

        // Set Rows Per Page on DataTable
        _this.pagination.rowsPerPage = users.length

        // If Imported Users - Activate View
        if (imported) {
          _this.$refs.CsvUsersImport.closeDialog()
          _this.settings.bShowContactMailData = true
          console.log('import created successfully')
        }
      }).catch(function (response) {
        // ERROR
        _this.bCreateUsersLoading = false
        _this.bCreateUsersDialog = false
      })
    },

    validUser (item) {
      return (this.pinIsOk(item) && this.panIsOk(item))
    },

    invalidUser (item) {
      return !this.validUser(item)
    },

    pinIsOk (item) {
      return item.pan && item.pan.pin && item.pan.pin.length === this.pinLength
    },

    panIsOk (item) {
      return item.pan && item.pan.pan && item.pan.pan.length == this.panLength
    },

    getOldUsersId (item) {
      return this.usersCreated.map(function (x) {
        return x.id
      }).indexOf(item.id)
    },

    copyObject (obj) {
      if (typeof obj != 'undefined') {
        var copy = JSON.parse(JSON.stringify(obj))
        if (copy) return copy
      }
    },

    isSaved (item) {
      var key = this.getOldUsersId(item)
      var itemL = this.copyObject(item)
      var itemR = this.copyObject(this.usersCreatedOld[key])

      if (itemL && itemR) {
        delete itemL.undefined
        delete itemR.undefined
        delete itemL.isSelected
        delete itemR.isSelected
        delete itemL.groupDialog
        delete itemR.groupDialog
        delete itemL.deleteUserDialog
        delete itemR.deleteUserDialog

        // Differences
        if (JSON.stringify(itemR) != JSON.stringify(itemL)) {
          // console.log(JSON.stringify(itemR), JSON.stringify(itemL));
          return false
        } else {
          return true
        }
      }

      return true
    },

    isUnsaved (item) {
      return !this.isSaved(item)
    },

    getUnsaved (arr) {
      var unsaved = []
      for (var i in arr) {
        var item = arr[i]

        if (this.isUnsaved(item)) {
          unsaved.push(item)
        }
      }
      return unsaved
    },

    resetUser (item) {
      var key = this.getOldUsersId(item)
      Object.assign(item, JSON.parse(JSON.stringify(this.usersCreatedOld[key])))
    },

    // trySendEntranceMail (user) {
    //   let pan = user.pan ?? {}
    //   let msg = pan && pan.pan ? 'Pan: "' + pan.pan : 'No Pan!'
    //   msg += pan && pan.contact_mail ? '" E-Mail: "' + pan.contact_mail : ' No Contact-Mail '
    //   msg += '"'

    //   this.$q.dialog({
    //     title: 'Send Entrance-Mail for User',
    //     message: msg,
    //     ok: {
    //       label: 'E-Mail versenden',
    //       color: 'negative',
    //       unelevated: true
    //     },
    //     cancel: {
    //       label: 'Abbruch',
    //       unelevated: true
    //     }
    //   }).onOk(() => {
    //     this.sendEntranceMail(user)
    //   })
    // },

    async sendEntranceMail (user) {
      if (user.pan && user.pan.contact_mail) {
        // Try Sending Mail
        this.loading = true

        this.$q.notify({
          message: this.$t('Try sending E-Mail to ' + user.pan.contact_mail),
          color: 'primary',
          position: 'top',
          timeout: 5000
        })

        await axios.post('/api/send-entrance-mail', {
          id: user.id,
          entrance: this.entranceMail
        }).then((e) => {
          this.$q.notify({
            message: this.$t('Success sending E-Mail to ' + user.pan.contact_mail),
            color: 'positive',
            position: 'top',
            timeout: 5000
          })
        }).catch((e) => {
          this.$q.notify({
            message: this.$t('Error sending E-Mail to ' + user.pan.contact_mail),
            caption: JSON.stringify(e),
            type: 'negative',
            position: 'top',
            timeout: 10000,
            progress: true
          })
        }).then((e) => {
          this.loading = false
          this.reloadUser(user)
        })
      } else {
        // No E-Mail
        this.$q.notify({
          message: this.$t('Error! No E-Mail at ' + user.pan.pan),
          caption: JSON.stringify(user.pan),
          type: 'warning',
          position: 'top',
          timeout: 10000,
          progress: true
        })
      }
    },

    trySendEntranceMails () {
      this.entranceMail.dialog = true
      this.entranceMail.checkbox = false
    },

    sendEntranceMails (users = this.selected) {
      for (var i in users) {
        var user = users[i]
        this.sendEntranceMail(user)
      }
    },

    reloadUser (user) {
      this.loading = true

      axios.post('/api/reload-user', {
        id: user.id
      }).then((response) => {
        let responseUser = response.data
        let key = this.getOldUsersId(responseUser)

        console.log(this.usersCreated)

        Object.assign(this.usersCreated[key], JSON.parse(JSON.stringify(responseUser)))
        Object.assign(this.usersCreatedOld[key], JSON.parse(JSON.stringify(responseUser)))

        console.log(this.usersCreated)
      }).catch((response) => {
        console.log('error reloading user', response)
      }).then(() => {
        this.loading = false
      })
    },

    updateUser (user) {
      this.updateUsers([user])
    },

    updateUsers (users) {
      // Variables
      var _this = this
      this.loading = true

      // Update Users
      this.$store.dispatch('users/updateUsers', {
        users: users
      }).then(function (e) {
        // Success
        if (!e || !e.response || !e.response.data || !e.response.data.error) {
          console.log('success!!')

          _this.$q.notify({
            message: _this.$t('data_saved'),
            color: 'green',
            position: 'top',
            timeout: 3000
          })

          // Save Old
          for (var i in users) {
            var user = users[i]
            var key = _this.getOldUsersId(user)
            Object.assign(_this.usersCreatedOld[key], JSON.parse(JSON.stringify(user)))
          }
        }
        _this.loading = false
      }).catch(function (e) {
        // Error
        if (!e || !e.response || !e.response.data) return
        var res = e.response.data
        var err = res.error
        var errText = ''
        for (var e in err) {
          errText += ': ' + err[e]
        }

        _this.$q.notify({
          message: _this.$t('attribute_unsaved') + '<br />' + errText,
          color: 'error',
          timeout: 6000
        })

        _this.loading = false
      })
    },

    findKeyById (arr, item) {
      return arr.findIndex(x => x.id === item.id)
    },

    findById (arr, id) {
      return arr.find(x => x.id === id)
    },

    deleteUsers (users) {
      // Variables
      var _this = this
      this.loading = true
      this.deleteUsersDialog = false

      // Delete User
      this.$store.dispatch('users/deleteUsers', {
        ids: users.map(user => user.id)
      }).then(function (e) {
        // Success
        _this.loading = false

        _this.$q.notify({
          message: _this.$t('data_saved'),
          color: 'green',
          timeout: 3000,
          position: 'top'
        })

        var tmpUsers = _this.copyObject(users)
        for (var i in tmpUsers) {
          var tmpUser = tmpUsers[i]
          _this.selected.splice(_this.findKeyById(_this.selected, tmpUser), 1)
          _this.usersCreatedOld.splice(_this.findKeyById(_this.usersCreatedOld, tmpUser), 1)
          _this.usersCreated.splice(_this.findKeyById(_this.usersCreated, tmpUser), 1)
        }
      })
    },

    save () {
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

    changePan (item) {
      if (item.pan && item.pan.pan) {
        item.pan.pan = item.pan.pan.toUpperCase()
        item.pan.pan = item.pan.pan.replace(' ', '')
      }
    },

    changePin (item) {

    },

    alreadyInGroups (group, groups) {
      if (!group || !groups || !groups.filter) return
      // item.groupsModerating && item.groupsModerating.includes(group)
      if (groups.filter(function (e) { return e.id === group.id }).length > 0) {
        return true
      }
      return false
    },

    addCreatedUserToGroup (user, group) {
      group.pivot = {
        is_mod: 0,
        is_member: 1
      }

      user.groups.push(group)
    },

    removeCreatedUserFromGroup (user, index) {
      user.groups.splice(index, 1)
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
