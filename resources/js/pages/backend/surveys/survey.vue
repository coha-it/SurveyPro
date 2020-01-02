<template>
  <div>
    <template>
      <q-btn
        icon="keyboard_arrow_left"
        small
        outline
        unelevated
        color="grey"
        rounded
        tag="router-link"
        :to="oBackRoute"
        class="small ml-auto my-auto"
        :label="$t('Back to surveys')"
      />

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
                  ]"
                />

      <br />
      <br />
    </template>

    <div v-if="oSurvey">
      <!-- Data Sheet -->
      <template>
        <q-form v-on:submit.prevent v-model="valid" ref="form" style="max-width: 1280px;">
          <q-toolbar color="primary" dark>
            <q-toolbar-title>
              <template v-if="bCreate">{{ "Neue Umfrage erstellen" }}</template>
              <template v-if="bEdit && oSurvey">{{ 'Umfrage' }} #{{ oSurvey.id }}</template>
            </q-toolbar-title>
          </q-toolbar>
          <!-- Basic Settings -->
          <q-tabs
            v-model="active_tab"
            :disable="surveyIsUneditable()"
            fixed-tabs
            icons-and-text
            align="justify"
            inline-label
            indicator-color="white"
            class="bg-primary text-white shadow-2"
          >
            <q-tab @click="changeTab('basis')" name="basis" label="Basis" icon="settings" />
            <q-tab
              @click="changeTab('fragen')"
              name="fragen"
              label="Fragen"
              icon="question_answer"
            />
            <q-tab @click="changeTab('gruppen')" name="gruppen" label="Gruppen" icon="group" />
          </q-tabs>

          <q-separator />

          <q-tab-panels v-model="active_tab" animated>
            <!-- Einstellungen -->
            <q-tab-panel name="basis">
              <q-list subheader two-line flat>
                <q-item-label>Textliche Einstellungen</q-item-label>

                <!-- Title -->
                <q-item>
                  <q-item-section>
                    <q-input
                      :disable="surveyIsUneditable()"
                      dense
                      persistent-hint
                      outlined
                      hint="Titel der Umfrage. Wird angezeigt"
                      v-model="oSurvey.title"
                      label="Title"
                      required
                      :rules="required"
                      :placeholder="oSurveyOld.title ? oSurveyOld.title : 'z.B.: &quot;Umfrage Mitarbeiterzufriedenheit&quot; '"
                    />
                  </q-item-section>
                </q-item>

                <!-- Author -->
                <q-item>
                  <q-item-section>
                    <q-input
                      :disable="surveyIsUneditable()"
                      dense
                      persistent-hint
                      outlined
                      hint="Author in Textform. Wird angezeigt"
                      :placeholder="oSurveyOld.author ? oSurveyOld.author : 'z.B.: &quot;Dr. Johannes Müller&quot; '"
                      v-model="oSurvey.author"
                      label="Autor"
                      required
                    />
                  </q-item-section>
                </q-item>

                <!-- Description Short -->
                <q-item>
                  <q-item-section>
                    <q-input
                      :disable="surveyIsUneditable()"
                      dense
                      persistent-hint
                      outlined
                      hint="Kurze Beschreibung der Umfrage"
                      v-model="oSurvey.desc_short"
                      label="Kurzbeschreibung"
                      required
                    />
                  </q-item-section>
                </q-item>

                <!-- Description Long -->
                <q-item>
                  <q-item-section>
                    <q-input
                      type="textarea"
                      :disable="surveyIsUneditable()"
                      outlined
                      dense
                      persistent-hint
                      v-model="oSurvey.desc_long"
                      hint="Lange Beschreibung der Umfrage"
                      label="Langbeschreibung"
                      required
                    />
                  </q-item-section>
                </q-item>

                <q-item-label>Konfigurations-Einstellungen</q-item-label>

                <q-item>
                  <q-item-section side top>
                    <q-checkbox
                      :disable="surveyIsUneditable()"
                      v-model="oSurvey.active"
                      color="primary"
                      :true-value="1"
                      :false-value="0"
                    />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Aktiviert</q-item-label>
                    <q-item-label
                      caption
                    >Ist diese Umfrage ausfüllbar (aktiviert) oder nicht ausfüllbar. Standard ist aktiviert.</q-item-label>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section side top>
                    <q-checkbox
                      :disable="surveyIsUneditable()"
                      v-model="oSurvey.only_editable_by_creator"
                      color="primary"
                      :true-value="1"
                      :false-value="0"
                    />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Nur für Ersteller Editierbar</q-item-label>
                    <q-item-label
                      caption
                    >Nur Sie selbst als Ersteller können diese Umfrage editieren. Im Standard sind Umfragen nur für Sie selbst editierbar</q-item-label>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section side top>
                    <q-checkbox
                      :disable="surveyIsUneditable()"
                      v-model="oSurvey.is_public"
                      color="red"
                      :true-value="1"
                      :false-value="0"
                    />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Öffentlich</q-item-label>
                    <q-item-label
                      caption
                    >Diese Umfrage ist öffentlich verfügbar. Im Standard sind Umfragen nicht öffentlich</q-item-label>
                  </q-item-section>
                </q-item>

                <template v-if="bEdit">
                  <q-item>
                    <q-item-section side top>
                      <q-checkbox
                        v-model="bExtendedSettings"
                        :disabled="oSurvey.is_finished == 1 || oSurvey.is_canceled == 1"
                        color="primary"
                      />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Erweiterte Einstellungen</q-item-label>
                      <q-item-label caption>Zeige erweiterte Einstellungen</q-item-label>
                    </q-item-section>
                  </q-item>

                  <template v-if="bExtendedSettings">
                    <q-item>
                      <q-item-section side top>
                        <q-checkbox
                          color="error"
                          v-model="oSurvey.is_finished"
                          :true-value="1"
                          :false-value="0"
                          :disable="surveyIsUneditable()"
                        />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Beendet</q-item-label>
                        <q-item-label caption>Die Umfrage wurde Beendet. Nicht umkehrbar</q-item-label>
                      </q-item-section>
                    </q-item>

                    <q-item>
                      <q-item-section side top>
                        <q-checkbox
                          :disable="surveyIsUneditable()"
                          color="error"
                          v-model="oSurvey.is_canceled"
                          :true-value="1"
                          :false-value="0"
                        />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Abgebrochen</q-item-label>
                        <q-item-label caption>Die Umfrage wurde abgebrochen. Nicht umkehrbar</q-item-label>
                      </q-item-section>
                    </q-item>
                  </template>
                </template>

                <q-item-label header>Datums-Einstellungen</q-item-label>

                <!-- Dates-->
                <q-item class="row q-gutter-md">
                  <div class="col col-12 col-sm-6 col-md-6">
                    <q-card>
                      <q-card-section>
                        <div class="text-h6">Beginn und Ende anpassen</div>
                      </q-card-section>

                      <q-card-section>
                        <p>Sobald ihre Umfrage beginnt - können Sie diese nicht mehr anpassen</p>

                        <div class="row">
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              :value="formatDate(oSurvey.start_datetime)"
                              :disable="surveyIsUneditable()"
                              label="Beginnt am"
                              placeholder="Bitte auswählen"
                              readonly
                              disabled
                              color="teal"
                              standout
                            >
                              <template v-slot:prepend>
                                <q-icon name="event_available" />
                              </template>
                              <q-popup-proxy
                                ref="qDateProxy"
                                transition-show="scale"
                                transition-hide="scale"
                              >
                                <q-date
                                  v-model="oSurvey.start_datetime"

                                  :events="events"
                                  :event-color="eventColor"
                                  :disable="surveyIsUneditable()"
                                  required
                                  :options="getMinStartDate"
                                  :selected-items-text="getDatesDiffDays() + ' Tage Zeit'"
                                  color="secondary"
                                  header-color="primary"
                                  mask="YYYY-MM-DD HH:mm:ss"
                                >
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn
                                      :label="$t('closer_button')"
                                      color="primary"
                                      flat
                                      v-close-popup
                                    />
                                  </div>
                                </q-date>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              v-if="oSurvey.start_datetime"
                              :value="formatTime(oSurvey.start_datetime)"
                              :disable="surveyIsUneditable()"
                              label="Beginnt um"
                              placeholder="Bitte auswählen"
                              readonly
                              :rules="required"
                              disabled
                              standout
                            >
                              <template v-slot:prepend>
                                <q-icon name="schedule" />
                              </template>
                              <q-popup-proxy
                                ref="qDateProxy"
                                transition-show="scale"
                                transition-hide="scale"
                              >
                                <q-time
                                  format24h
                                  v-model="oSurvey.start_datetime"
                                  :disable="surveyIsUneditable()"
                                  :options="getMinStartTime"
                                  mask="YYYY-MM-DD HH:mm:ss"
                                  color="green"
                                >
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn
                                      :label="$t('closer_button')"
                                      color="primary"
                                      flat
                                      v-close-popup
                                    />
                                  </div>
                                </q-time>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                        </div>

                        <div class="row" v-if="oSurvey.start_datetime">
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              :value="formatDate(oSurvey.end_datetime)"
                              :disable="surveyIsUneditable()"
                              label="Endet am"
                              readonly
                              :rules="required"
                              disabled
                              standout
                            >
                              <template v-slot:prepend>
                                <q-icon name="event_busy" />
                              </template>
                              <q-popup-proxy
                                ref="qDateProxy"
                                transition-show="scale"
                                transition-hide="scale"
                              >
                                <q-date
                                  v-model="oSurvey.end_datetime"

                                  :events="events"
                                  :event-color="eventColor"
                                  :disable="surveyIsUneditable()"
                                  required
                                  :options="getMaxEndDate"
                                  range
                                  color="grey"
                                  header-color="primary"
                                  mask="YYYY-MM-DD HH:mm:ss"
                                >
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn
                                      :label="$t('closer_button')"
                                      color="primary"
                                      flat
                                      v-close-popup
                                    />
                                  </div>
                                </q-date>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              :value="formatTime(oSurvey.end_datetime)"
                              :disable="surveyIsUneditable()"
                              label="Endet um"
                              readonly
                              :rules="required"
                              disabled
                              icon="schedule"
                              standout
                            >
                              <template v-slot:prepend>
                                <q-icon name="timelapse" />
                              </template>
                              <q-popup-proxy
                                ref="qDateProxy"
                                transition-show="scale"
                                transition-hide="scale"
                              >
                                <q-time
                                  format24h
                                  v-model="oSurvey.end_datetime"
                                  :disable="surveyIsUneditable()"
                                  mask="YYYY-MM-DD HH:mm:ss"
                                  color="grey"
                                  :options="getMaxEndTime"
                                >
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn
                                      :label="$t('closer_button')"
                                      color="primary"
                                      flat
                                      v-close-popup
                                    />
                                  </div>
                                </q-time>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                        </div>

                        <div class="row justify-center full-width">
                          <div class="col">
                            <q-chat-message
                              :text="['Hallo! Die Umfrage ersteckt sich über folgende Zeit: <br/><strong>'+ getDiffDatetimeLabel() + '</strong>. Startend am ' + formatDate(oSurvey.start_datetime) + ' bis zum ' + formatDate(oSurvey.end_datetime)  ]"
                              sent
                              :bg-color="getDiffDatetimeColor()"
                              text-color="white"
                              size="8"
                            >
                              <q-btn
                                slot="avatar"
                                disable
                                flat
                                round
                                readonly
                                unelevated
                                icon="timer"
                              />
                            </q-chat-message>
                          </div>
                        </div>
                      </q-card-section>
                    </q-card>
                  </div>

                  <div class="col col-12 col-sm-6 col-md-6">
                    <q-date
                      :events="events"
                      :event-color="eventColor"
                      :options="events"
                      :value="sTodayDatetime"
                      :title="getDiffDatetimeLabel()"
                      subtitle="Zeit für die Umfrage"
                    />
                  </div>
                </q-item>
              </q-list>
            </q-tab-panel>

            <!-- Questions -->
            <q-tab-panel name="fragen">
              <q-list>
                <q-item>
                  <q-item-section>
                    <!-- No Select Toolbar -->
                    <q-toolbar
                      v-if="selected.length <= 0"
                      class="coha--toolbar"
                      :flat="sSearch == ''"
                      floating
                      style="min-height:100px"
                      height="auto"
                    >
                      <div class="flex-grow-1"></div>
                      <q-input
                        v-model="sSearch"
                        style="max-width: 400px;"
                        :label="$t('Search')"
                        autocomplete="off"
                        append-icon="search"
                        hide-details
                        outlined
                      />
                      <q-input
                        v-model="pagination.rowsPerPage"
                        number
                        type="number"
                        hide-details
                        style="max-width: 150px;"
                        label="Zeilen pro Seite"
                        class="ml-5"
                        outlined
                      />
                    </q-toolbar>

                    <q-toolbar
                      v-else
                      class="coha--toolbar bg-primary text-white"
                      :flat="sSearch == ''"
                      color="primary"
                      dark
                      floating
                      style="min-height:100px"
                      height="auto"
                    >
                      <q-btn :label="selected.length + ' ' + $t('edit')" unelevated rounded icon="mdi-pencil" v-on="{ ...menuedit }">
                        <q-menu right offset-y>
                          <q-list style="min-width: 100px">
                            <q-item v-close-popup clickable @click="duplicateSelectedQuestions()">
                              <q-item-section side>
                                <q-icon name="control_point_duplicate" />
                              </q-item-section>
                              <q-item-section>
                                Duplizieren
                              </q-item-section>
                            </q-item>
                            <q-item v-close-popup clickable @click="bDeleteQuestionDialog = true">
                              <q-item-section side>
                                <q-icon name="delete" />
                              </q-item-section>
                              <q-item-section>
                                Löschen
                              </q-item-section>
                            </q-item>
                            <q-item clickable @click="moveSelectedUp()">
                              <q-item-section side>
                                <q-icon name="arrow_drop_up" />
                              </q-item-section>
                              <q-item-section>
                                Position Hoch
                              </q-item-section>
                            </q-item>
                            <q-item clickable @click="moveSelectedDown()">
                              <q-item-section side>
                                <q-icon name="arrow_drop_down" />
                              </q-item-section>
                              <q-item-section>
                                Position Runter
                              </q-item-section>
                            </q-item>
                          </q-list>
                        </q-menu>
                      </q-btn>
                      <div class="flex-grow-1"></div>
                    </q-toolbar>

                    <!-- Delete - Dialog -->
                    <q-dialog
                      v-model="bDeleteQuestionDialog"
                      max-width="500"
                      dark
                      content-class="naked dark centered"
                    >
                      <h2 class="display-2">Fragen Löschen?</h2>
                      <p>Möchten Sie {{ selected.length }} Fragen löschen?</p>
                      <div class="container fluid">
                        <div class="row" align="center">
                          <div class="col text-center" cols="12" sm="12">
                            <q-btn depressed @click="bDeleteQuestionDialog = false" outlined>Abbruch</q-btn>
                            <q-btn
                              depressed
                              color="error"
                              @click.prevent="deleteQuestions(selected)"
                            >Löschen</q-btn>
                          </div>
                        </div>
                      </div>
                    </q-dialog>

                    <q-table
                      v-model="selected"
                      :columns="headers"
                      :data="oSurvey.questions"
                      dense
                      multi-sort
                      selection="multiple"
                      show-select
                      :selected.sync="selected"
                      show-expand
                      :expanded.sync="expanded"
                      :search="sSearch"
                      :sort-by="['order']"
                      :sort-desc="[false]"
                      :pagination.sync="pagination"
                      :footer-props="{
                        showFirstLastPage: true,
                      }"
                      class="my-data-table f-height questions-table"
                    >
                      <template v-slot:body="props">
                        <q-tr :props="props">
                          <!-- Selected -->
                          <q-td>
                            <q-checkbox v-model="props.selected" dense />
                          </q-td>

                          <q-td key="order" :props="props">
                            <div style="white-space: nowrap;">
                              <q-icon
                                name="mdi-arrow-up"
                                x-small
                                @click="moveUp(props.row, oSurvey.questions)"
                              />
                              {{ props.row.order }}
                              <q-icon
                                name="mdi-arrow-down"
                                x-small
                                @click="moveDown(props.row, oSurvey.questions)"
                              />
                            </div>
                          </q-td>

                          <q-td key="title" :props="props">
                            {{ props.row.title }}
                            <q-popup-edit
                              v-model="props.row.title"
                              buttons
                              single-line
                              persistent
                              :cover="false"
                              self="center left"
                              anchor="center right"
                              :offset="[5, 0]"
                              @save="save(props.row)"
                            >
                              <q-input
                                v-model="props.row.title"
                                :label="$t('title')"
                                counter
                                autofocus
                              />
                            </q-popup-edit>
                          </q-td>

                          <q-td key="subtitle" :props="props">
                            {{ props.row.subtitle }}
                            <q-popup-edit
                              v-model="props.row.subtitle"
                              buttons
                              single-line
                              persistent
                              :cover="false"
                              self="center left"
                              anchor="center right"
                              :offset="[5, 0]"
                              @save="save(props.row)"
                            >
                              <q-input
                                v-model="props.row.subtitle"
                                :label="$t('subtitle')"
                                counter
                                autofocus
                              />
                            </q-popup-edit>
                          </q-td>

                          <q-td key="description" :props="props">
                            {{ props.row.description }}
                            <q-popup-edit
                              v-model="props.row.description"
                              buttons
                              single-line
                              persistent
                              :cover="false"
                              self="center left"
                              anchor="center right"
                              :offset="[5, 0]"
                              @save="save(props.row)"
                            >
                              <q-input
                                v-model="props.row.description"
                                :label="$t('description')"
                                counter
                                autofocus
                              />
                            </q-popup-edit>
                          </q-td>

                          <q-td key="is_skippable" :props="props">
                            <q-checkbox
                              v-model="props.row.is_skippable"
                              :true-value="1"
                              :false-value="0"
                            />
                          </q-td>

                          <q-td key="is_commentable" :props="props">
                            <q-checkbox
                              v-model="props.row.is_commentable"
                              :true-value="1"
                              :false-value="0"
                            />
                          </q-td>

                          <q-td key="min_options" :props="props">
                            {{ props.row.min_options }}
                            <q-popup-edit
                              v-model="props.row.min_options"
                              buttons
                              single-line
                              persistent
                              :cover="false"
                              self="center left"
                              anchor="center right"
                              :offset="[5, 0]"
                              @save="save(props.row)"
                            >
                              <q-input
                                v-model="props.row.min_options"
                                :label="$t('min_options')"
                                :max="parseInt(props.row.max_options)"
                                autofocus
                                type="number"
                              />
                            </q-popup-edit>
                          </q-td>

                          <q-td key="max_options" :props="props">
                            {{ props.row.max_options }}
                            <q-popup-edit
                              v-model="props.row.max_options"
                              buttons
                              single-line
                              persistent
                              :cover="false"
                              self="center left"
                              anchor="center right"
                              :offset="[5, 0]"
                              @save="save(props.row)"
                            >
                              <q-input
                                v-model="props.row.max_options"
                                :label="$t('max_options')"
                                :min="parseInt(props.row.min_options)"
                                :max="10"
                                autofocus
                                type="number"
                              />
                            </q-popup-edit>
                          </q-td>

                          <q-td key="expand" :props="props">
                            <q-btn
                              dense
                              round
                              flat
                              :icon="questionIsExpanded(props.row.id) ? 'arrow_drop_up' : 'arrow_drop_down'"
                              @click="expandQuestion(props)"
                            />
                          </q-td>
                        </q-tr>

                        <!-- Expandable Area -->
                        <!-- v-show="props.expand" -->
                        <q-tr v-show="questionIsExpanded(props.row.id)" :props="props" class="expandable-row">
                          <q-td colspan="100%">
                            <div class="text-left">
                              <template v-if="props.row">
                                <div colspan="100%" style="display: contents; display: initial">
                                  <br>
                                  <div class="row">
                                    <div class="col" xl="12" sm="12" xs="12">
                                      <q-list subheader two-line flat>
                                        <q-item-label header>
                                          Allgemeine Frage-Einstellungen für Frage-ID: #{{ props.row.id }}
                                        </q-item-label>
                                        <q-item>
                                          <q-item-section side top>
                                            <q-checkbox
                                              v-model="props.row.is_skippable"
                                              :disable="surveyIsUneditable()"
                                              color="primary"
                                              :true-value="1"
                                              :false-value="0"
                                            />
                                          </q-item-section>
                                          <q-item-section>
                                            <q-item-label>Überspringbar</q-item-label>
                                            <q-item-label caption>
                                              Ist diese Frage überspringbar
                                            </q-item-label>
                                          </q-item-section>
                                        </q-item>

                                        <q-item>
                                          <q-item-section>
                                            <q-input
                                              v-model="props.row.title"
                                              :disable="surveyIsUneditable()"
                                              dense
                                              persistent-hint
                                              outlined
                                              placeholder="z.B.: 'Fazit zur Umfrage' "
                                              label="Titel"
                                              required
                                            >
                                              <template v-slot:append>
                                                <q-icon :name="help_icon">
                                                  <q-tooltip self="center middle">
                                                    Titel der Frage. Wird am größten angezeigt!
                                                  </q-tooltip>
                                                </q-icon>
                                              </template>
                                            </q-input>
                                          </q-item-section>
                                        </q-item>

                                        <q-item>
                                          <q-item-section>
                                            <q-input
                                              v-model="props.row.subtitle"
                                              :disable="surveyIsUneditable()"
                                              dense
                                              persistent-hint
                                              outlined
                                              placeholder="z.B.: 'Wie fanden Sie diese Umfrage?' "
                                              label="Untertitel"
                                              required
                                            >
                                              <template v-slot:append>
                                                <q-icon :name="help_icon">
                                                  <q-tooltip self="center middle">
                                                    Subtitel / Untertitel der Frage. Wird unter Titel angezeigt
                                                  </q-tooltip>
                                                </q-icon>
                                              </template>
                                            </q-input>
                                          </q-item-section>
                                        </q-item>

                                        <q-item>
                                          <q-item-section>
                                            <q-input
                                              v-model="props.row.description"
                                              type="textarea"
                                              :disable="surveyIsUneditable()"
                                              dense
                                              persistent-hint
                                              outlined
                                              placeholder="z.B.: 'Bewerten Sie diese Umfrage bitte mit 0 (negativ) bis 10 (positiv) Punkten' "
                                              label="Beschreibung"
                                              required
                                            >
                                              <template v-slot:append>
                                                <q-icon :name="help_icon">
                                                  <q-tooltip self="center middle">
                                                    Beschreibung der Umfrage. Wird unter Titel / Subtitel klein angezeigt.
                                                  </q-tooltip>
                                                </q-icon>
                                              </template>
                                            </q-input>
                                          </q-item-section>
                                        </q-item>
                                      </q-list>
                                    </div>

                                    <div class="col" xl="12" sm="12" xs="12">
                                      <q-list subheader two-line flat>
                                        <q-item-label header>Kommentar-Einstellungen</q-item-label>

                                        <q-item>
                                          <q-item-section side top>
                                            <q-checkbox
                                              v-model="props.row.is_commentable"
                                              :disable="surveyIsUneditable()"
                                              color="primary"
                                              :true-value="1"
                                              :false-value="0"
                                            />
                                          </q-item-section>
                                          <q-item-section>
                                            <q-item-label>Kommentierbar</q-item-label>
                                          </q-item-section>
                                        </q-item>

                                        <template v-if="props.row.is_commentable || true">
                                          <q-item>
                                            <q-item-section side top>
                                              <q-checkbox
                                                v-model="props.row.comment_is_required"
                                                :disable="surveyIsUneditable() && props.row.is_commentable"
                                                color="primary"
                                                :true-value="1"
                                                :false-value="0"
                                              />
                                            </q-item-section>
                                            <q-item-section>
                                              <q-item-label>Kommentar ist erforderlich</q-item-label>
                                            </q-item-section>
                                          </q-item>

                                          <q-item>
                                            <q-item-section side top>
                                              <q-checkbox
                                                v-model="props.row.comment_is_number"
                                                :disable="surveyIsUneditable()"
                                                color="primary"
                                                :true-value="1"
                                                :false-value="0"
                                              />
                                            </q-item-section>
                                            <q-item-section>
                                              <q-item-label>Kommentar ist eine Nummer</q-item-label>
                                            </q-item-section>
                                          </q-item>

                                          <q-item>
                                            <q-item-section>
                                              <q-input
                                                v-model="props.row.comment_max_signs"
                                                :disable="surveyIsUneditable()"
                                                dense
                                                persistent-hint
                                                outlined
                                                placeholder="1 - 255"
                                                type="number"
                                                label="Kommentar: Maximale Zeichen"
                                                required
                                              >
                                                <template v-slot:append>
                                                  <q-icon :name="help_icon">
                                                    <q-tooltip
                                                      self="center middle"
                                                    >Wieviele einzelne Zeichen darf ein Kommentar maximal haben?</q-tooltip>
                                                  </q-icon>
                                                </template>
                                              </q-input>
                                            </q-item-section>
                                          </q-item>
                                        </template>
                                      </q-list>
                                      <q-list subheader two-line flat>
                                        <q-item-label header>Frage-Optionen</q-item-label>
                                        <q-item>
                                          <q-item-section>
                                            <div class="row">
                                              <div class="col" xl="3" md="3" sm="6" xs="12">
                                                <q-input
                                                  v-model="props.row.min_options"
                                                  :disable="surveyIsUneditable()"
                                                  dense
                                                  outlined
                                                  placeholder="1 - 255"
                                                  type="number"
                                                  :max="parseInt(props.row.max_options)"
                                                  label="Minimale Optionen"
                                                  required
                                                >
                                                  <template v-slot:append>
                                                    <q-icon :name="help_icon">
                                                      <q-tooltip
                                                        self="center middle"
                                                      >Minimal wählbare Optionen</q-tooltip>
                                                    </q-icon>
                                                  </template>
                                                </q-input>
                                              </div>
                                              <div class="col" xl="3" md="3" sm="6" xs="12">
                                                <q-input
                                                  v-model="props.row.max_options"
                                                  :disable="surveyIsUneditable()"
                                                  dense
                                                  persistent-hint
                                                  outlined
                                                  hint="Maximale wählbare Optionen"
                                                  placeholder="1 - 255"
                                                  type="number"
                                                  :min="parseInt(props.row.min_options)"
                                                  label="Maximale Optionen"
                                                  required
                                                />
                                              </div>
                                            </div>
                                          </q-item-section>
                                        </q-item>
                                        <q-item>
                                          <q-item-section>
                                            <!-- -->
                                            <template>
                                              <!-- Selected Toolbar -->
                                              <q-toolbar
                                                v-if="aSelectedOptions && aSelectedOptions.length"
                                                class="coha--toolbar"
                                                :flat="sSearch == ''"
                                                color="primary"
                                                dark
                                                floating
                                                min-height="85px"
                                                height="auto">
                                                <q-btn
                                                  v-if="aSelectedOptions && aSelectedOptions.length"
                                                  depressed
                                                  color="red"
                                                  dark
                                                  @click="bDeleteOptionDialog = true"
                                                >
                                                  <q-icon name="delete" left />&nbsp;
                                                  Ausgewählte Optionen Löschen
                                                </q-btn>
                                              </q-toolbar>

                                              <!-- No Selected Toolbar -->
                                              <q-toolbar
                                                v-else
                                                class="coha--toolbar"
                                                :flat="sSearch == ''"
                                                floating
                                                min-height="85px"
                                                height="auto">
                                                <div class="flex-grow-1"></div>
                                              </q-toolbar>

                                              <q-table
                                                v-model="aSelectedOptions"
                                                :columns="aOptionHeaders"
                                                :data="props.row.options"
                                                dense
                                                multi-sort
                                                selection="multiple"
                                                :items-per-page="parseInt(iOptionsPerPage)"
                                                :sort-by="['order']"
                                                :sort-desc="[false]"
                                                :footer-props="{
                                                  showFirstLastPage: true,
                                                }"
                                                class="my-data-table f-height options-table"
                                                >
                                                <template v-slot:item.value="props">
                                                  <q-popup-edit v-model="props.item.value">
                                                    {{ props.item.value }}
                                                    <q-input
                                                      v-model="props.item.value"
                                                      label="Edit"
                                                      counter
                                                    />
                                                  </q-popup-edit>
                                                </template>

                                                <template v-slot:item.title="props">
                                                  <q-popup-edit v-model="props.item.title">
                                                    {{ props.item.title }}
                                                    <q-input
                                                      v-model="props.item.title"
                                                      label="Edit"
                                                      counter
                                                    />
                                                  </q-popup-edit>
                                                </template>

                                                <template v-slot:item.subtitle="props">
                                                  <q-popup-edit v-model="props.item.subtitle">
                                                    {{ props.item.subtitle }}
                                                    <q-input
                                                      v-model="props.item.subtitle"
                                                      label="Edit"
                                                      counter
                                                    />
                                                  </q-popup-edit>
                                                </template>

                                                <template v-slot:item.description="props">
                                                  <q-popup-edit v-model="props.item.description">
                                                    {{ props.item.description }}
                                                    <q-input
                                                      v-model="props.item.description"
                                                      label="Edit"
                                                      counter
                                                    />
                                                  </q-popup-edit>
                                                </template>

                                                <template v-slot:item.order="props">
                                                  <div style="white-space: nowrap;">
                                                    <q-icon name="arrow-up" x-small @click="moveUp(props.item, item.options)" />
                                                    {{ props.item.order }}
                                                    <q-icon x-small name="arrow-down" @click="moveDown(props.item, item.options)" />
                                                  </div>
                                                </template>
                                                <template v-slot:item.color="props">
                                                  <q-dialog v-model="props.item.dialog" max-width="290">
                                                    <template v-slot:activator="{ on }">
                                                      <div>
                                                        <q-btn
                                                          :color="props.item.color"
                                                          small
                                                          depressed
                                                          fab
                                                          dark
                                                          v-on="on"
                                                        >
                                                          <q-icon name="palette" dark />
                                                        </q-btn>
                                                      </div>
                                                      <div>{{ props.item.color }}</div>
                                                    </template>
                                                    <q-card>
                                                      <q-card-section>Farbe auswählen</q-card-section>
                                                      <q-card-section>
                                                        <q-btn
                                                          block
                                                          small
                                                          :color="props.item.color"
                                                        >{{ props.item.color }}</q-btn>
                                                        <template v-for="oColor in aAllOptionColors">
                                                          <div
                                                            style="margin-top: 5px;"
                                                            v-bind:key="oColor.title"
                                                          >
                                                            <q-btn
                                                              block
                                                              small
                                                              :color="oColor.hex"
                                                              @click="props.item.color = oColor.hex"
                                                            >{{ oColor.title }}</q-btn>
                                                          </div>
                                                        </template>
                                                      </q-card-section>
                                                      <!-- <v-color-picker
                                                        v-model="props.item.color"
                                                        mode="rgba"
                                                        value="#666666"
                                                        flat
                                                      ></v-color-picker> -->
                                                      <q-card-section>
                                                        <q-btn @click="props.item.dialog = false">Übernehmen und schließen</q-btn>
                                                      </q-card-section>
                                                    </q-card>
                                                  </q-dialog>
                                                </template>
                                              </q-table>
                                              <q-btn
                                                label="Neue Option hinzufügen"
                                                icon="plus_one"
                                                @click="addNewOption(props.row)"
                                              />&nbsp;
                                              <q-btn color="primary" @click="duplicateLastOption(item)">
                                                <q-icon name="control_point_duplicate" left />Letzte Option duplizieren
                                              </q-btn>

                                              <!-- Delete - Dialog -->
                                              <q-dialog
                                                v-model="bDeleteOptionDialog"
                                                max-width="500"
                                                dark
                                                content-class="naked dark centered"
                                                >
                                                <h2 class="display-2">Optionen Löschen?</h2>
                                                <p>Möchten Sie {{ aSelectedOptions.length }} Optionen löschen?</p>
                                                <div class="container fluid">
                                                  <div class="row" align="center">
                                                    <div class="col text-center" cols="12" sm="12">
                                                      <q-btn
                                                        depressed
                                                        outlined
                                                        @click="bDeleteOptionDialog = false"
                                                      >Abbruch</q-btn>
                                                      <q-btn
                                                        depressed
                                                        color="error"
                                                        @click.prevent="deleteOptions(item, aSelectedOptions)"
                                                      >Löschen</q-btn>
                                                    </div>
                                                  </div>
                                                </div>
                                              </q-dialog>
                                            </template>
                                          </q-item-section>

                                        </q-item>
                                      </q-list>
                                    </div>
                                  </div>
                                </div>
                              </template>
                            </div>
                          </q-td>
                        </q-tr>
                      </template>
                    </q-table>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-btn name="Neue Frage hinzufügen" icon="plus_one" @click="addNewQuestion()" />&nbsp; &nbsp;
                  <q-btn
                    :disabled="oSurvey.questions.length <= 0"
                    color="primary"
                    @click="duplicateLastQuestion()"
                  >
                    <q-icon name="control_point_duplicate" left />
                    Letzte Frage duplizieren
                  </q-btn>
                </q-item>

                <q-separator />
              </q-list>
            </q-tab-panel>

            <!-- Gruppeneinstellungen -->
            <q-tab-panel name="gruppen">
              <q-list subheader two-line flat>
                <q-item-label>Gruppen</q-item-label>

                <!-- Title -->
                <q-item>
                  <q-item-section>
                    <q-select
                      v-model="oSurvey.groups"
                      :options="aFilteredGroups"
                      option-value="id"
                      option-label="name"
                      label="Gruppen Auswählen"
                      outlined
                      dense
                      options-dense
                      use-input
                      fill-input
                      input-debounce="0"
                      use-chips
                      stack-label
                      multiple
                      return-object
                      :disable="surveyIsUneditable()"
                      @filter="filterGroups"
                    >
                      <template v-slot:option="scope">
                        <q-item
                          v-if="!findById(oSurvey.groups, scope.opt.id)"
                          v-bind="scope.itemProps"
                          v-on="scope.itemEvents"
                        >
                          <q-item-section avatar>
                            <q-icon name="group" />
                          </q-item-section>
                          <q-item-section>
                            <q-item-label v-html="scope.opt.name" />
                            <q-item-label caption>
                              {{ scope.opt.description_public }}
                            </q-item-label>
                          </q-item-section>
                        </q-item>
                      </template>
                    </q-select>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-tab-panel>
          </q-tab-panels>

          <q-list>
            <q-item>
              <q-input v-model="oSurvey.title" required :rules="required" style="display: none;" />

              <q-btn color="grey" dark class="mr-4">Zurück</q-btn>
              <q-btn
                color="green"
                type="submit"
                @click="updateSurvey()"
                class="mr-4 white--text"
                v-if="surveyIsEditable()"
                :disabled="surveyFormIsInvalid()"
              >Umfrage Speichern {{ isUnsaved() ? '*' : undefined }}</q-btn>

              <!-- Save as FAB -->
              <q-page-sticky bottom right :open-on-hover="true" v-model="bFabButtonInner" fixed>
                <template v-slot:activator v-slot:extension>
                  <q-page-sticky>
                    <q-btn
                      v-show="isUnsaved()"
                      v-model="bFabButtonInner"
                      color="success"
                      dark
                      fab
                      type="submit"
                      @click="updateSurvey()"
                      :icon="bFabButtonInner ? 'mdi-content-save' : false"
                    ></q-btn>
                  </q-page-sticky>
                </template>
                <q-btn fab dark small color="warning" icon="mdi-restore" />
              </q-page-sticky>
            </q-item>
          </q-list>
        </q-form>
      </template>
    </div>

    <!-- Dialog Loading -->
    <q-dialog
      v-model="bIsLoading"
      max-width="500"
      dark
      content-class="naked dark centered"
      persistent
    >
      <q-circular-progress indeterminate />
      <div>{{ $t('loading.text') }}</div>
    </q-dialog>
  </div>
</template>

<script>
import axios from "axios"
import { mapGetters } from "vuex"
import moment from "moment"
import UserDataModal from '~/components/BackendUserDataModal'

export default {

  components: {
    UserDataModal
  },

  data() {
    return {

      // Questions Extended (local storage)
      questions_extended: [],

      // Icons
      help_icon: 'help_outline',

      // Timeing
      range: '',
      min: '',
      max: '',

      // FAB
      bFabButtonInner: false,

      // Questions
      bDeleteQuestionDialog: false,
      sSearch: '',
      pagination: {
        sortBy: 'order',
        descending: false,
        page: 1,
        rowsPerPage: 100
      },
      headers: [
        {
          label: 'Reihenfolge',
          align: 'left',
          name: 'order',
          field: 'order',
          sortable: true
        },
        {
          label: 'Title',
          align: 'left',
          name: 'title',
          field: 'title',
          sortable: true
        },
        {
          label: 'Untertitel',
          align: 'left',
          name: 'subtitle',
          field: 'subtitle',
          sortable: true
        },
        {
          label: 'Beschreibung',
          align: 'left',
          name: 'description',
          field: 'description',
          sortable: true
        },
        {
          label: 'Überspringbar',
          name: 'is_skippable',
          field: 'is_skippable',
          align: null,
          sortable: true
        },
        {
          label: 'Kommentierbar',
          name: 'is_commentable',
          field: 'is_commentable',
          align: null,
          sortable: true
        },
        {
          label: 'Mind. Optionen',
          name: 'min_options',
          field: 'min_options',
          align: null,
          sortable: true
        },
        {
          label: 'Max Optionen',
          name: 'max_options',
          field: 'max_options',
          align: null,
          sortable: true
        },
        {
          label: '',
          name: 'expand',
          field: 'expand',
          align: null,
          sortable: false
        }
      ],
      selected: [],
      expanded: [],

      // Groups
      aFilteredGroups: [],

      // Options
      aOptionHeaders: [
        {
          label: '',
          name: 'select',
          field: 'select',
          value: 'select',
          align: 'left',
          sortable: false
        },
        {
          label: 'Reihenfolge',
          name: 'order',
          field: 'order',
          align: 'left'
        },
        {
          label: 'Technischer Wert',
          name: 'value',
          field: 'value',
          align: 'left'
        },
        {
          label: 'Title',
          name: 'title',
          field: 'title',
          align: 'left'
        },
        {
          label: 'Untertitel',
          name: 'subtitle',
          field: 'subtitle',
          align: 'left'
        },
        {
          label: 'Beschreibung',
          name: 'description',
          field: 'description',
          align: 'left'
        },
        {
          label: 'Color',
          name: 'color',
          field: 'color',
          align: 'center'
        }
      ],
      bDeleteOptionDialog: false,
      iOptionsPerPage: 50,

      // To Delete
      aDeleteQuestionsIds: [],
      aDeleteOptionsIds: [],

      // Options
      aSelectedOptions: [],

      // Loading
      bIsLoading: false,

      // Tabs
      active_tab: 'basis',

      // Back Route
      oBackRoute: { name: 'backend.surveys' },
      bCreate: false,
      bEdit: false,
      bExtendedSettings: false,

      // Form
      valid: true,
      required: [v => !!v || this.$t('required')],

      // Dates and Times
      oTimes: {
        sStartTime: '',
        sEndTime: ''
      },

      // Today
      sTodayDate: moment().format('YYYY-MM-DD'),
      sTodayTime: moment().format('HH:mm:ss'),
      sTodayDatetime: moment().format('YYYY-MM-DD HH:mm:ss'),

      // Tmps Start
      // sStartDate: '', // this.getStartDate(),

      // Tmps End
      // sEndDate: '', // this.getEndDate(),

      // Surveys
      oSurvey: null,
      oSurveyOld: null,

      // All Question Option Colors
      aAllOptionColors: [
        {
          title: 'Grey',
          hex: '#C6C6C6'
        },
        {
          title: 'Green',
          hex: '#6CAE75'
        },
        {
          title: 'Red',
          hex: '#D09786'
        }
      ]
    };
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      surveyAllowed: 'surveys/surveyAllowed'
    })
  },

  watch: {
    surveyAllowed (promise) {
      // save Promise result in local state
      this.oSurvey = this.copyObject(promise)
      this.oSurveyOld = this.copyObject(promise)

      // Go
      // if(this.oSurvey.start_datetime)  this.aDates[0] = this.oSurvey.start_datetime.substr(0, 10);
      // if(this.oSurvey.end_datetime)    this.aDates[1] = this.oSurvey.end_datetime.substr(0, 10);
      // this.oTimes.sStartTime = this.oSurvey.start_datetime.substr(11, 5);
      // this.oTimes.sEndTime   = this.oSurvey.end_datetime.substr(11, 5);

      // If Something inside bExtendedSettings is active
      if (this.oSurvey.is_finished || this.oSurvey.is_canceled) {
        this.bExtendedSettings = true
      }

      // Go Through it and reorder it!
      this.reorderQuestions()
    },

    oSurvey: {
      handler () {
        // console.log('oSurvey Changed');
        // this.$refs.form.validate();
      },
      deep: true
    },

    aDates: {
      handler () {
        // this.updateDatetimes();
      }
    },

    oTimes: {
      handler () {
        // this.updateDatetimes();
      },
      deep: true
    },

    questions_extended: {
      handler: function (val) {
        localStorage.sQuestionsExtended = JSON.stringify(val)
      },
      deep: true
    }
  },

  created: function () {
    var route = this.$route
    var params = route.params
    var id = parseInt(params.id)

    if (params.id == 'create') {
      this.startCreateMode()
    } else if (typeof id == 'number') {
      this.$store.dispatch('surveys/fetchSurveyAllowed', id)
      this.startEditMode()
    } else {
      this.$router.push(this.oBackRoute)
    }

    // Check Tab for Hash
    this.checkTabForHash()
  },

  mounted () {
    var sQe = localStorage.sQuestionsExtended
    this.questions_extended = (sQe ? JSON.parse(sQe) : [])
  },

  methods: {

    findById (arr, id) {
      return arr.find(x => x.id === id)
    },

    filterGroups (val, update, abort) {
      this.aFilteredGroups = this.user.groups_moderating
      update(() => {
        const needle = val.toLowerCase()
        this.aFilteredGroups = this.user.groups_moderating.filter(v => v.name.toLowerCase().indexOf(needle) > -1)
      })
    },

    questionIsExpanded (id) {
      return this.questions_extended.find(e => e == id)
    },

    expandQuestion (question) {
      // TMP arr
      var arr = this.questions_extended
      var id = question.row.id.toString()

      if (arr.includes(id)) {
        // Unexpand!
        arr = arr.filter(e => e !== id)
      } else {
        // Expand!
        arr.push(id)
      }

      this.questions_extended = arr // Set new Array
    },

    save () {
      this.$q.notify({
        message: this.$t('attribute_changed'),
        color: 'primary',
        timeout: 3000
      })
    },

    orderedOptions: function (options) {
      return options.sort((a, b) => (a.order > b.order ? 1 : -1))
    },

    getStartDate () {
      return moment()
        .format()
        .substr(0, 10)
    },

    // getEndDate() {
    //   return moment(moment() + 5).format().substr(0, 10)
    // },

    reorderQuestions () {
      var oQuestions = this.oSurvey.questions
      for (var i in oQuestions) {
        if (oQuestions.hasOwnProperty(i)) {
          var oQuestion = oQuestions[i]
          oQuestion.order = parseInt(i) + 1
        }
      }
    },

    reorderOptions (question) {
      var oOptions = question.options
      for (var i in oOptions) {
        if (oOptions.hasOwnProperty(i)) {
          var oOption = oOptions[i]
          oOption.order = parseInt(i) + 1
        }
      }
    },

    events (d) {
      var entry = moment(d).format('YYYY-MM-DD')
      var start = moment(this.oSurvey.start_datetime).format('YYYY-MM-DD')
      var end = moment(this.oSurvey.end_datetime).format('YYYY-MM-DD')

      return entry >= start && entry <= end
    },

    eventColor (d) {
      var entry = moment(d).format('YYYY-MM-DD')
      var start = moment(this.oSurvey.start_datetime).format('YYYY-MM-DD')
      var end = moment(this.oSurvey.end_datetime).format('YYYY-MM-DD')

      switch (entry) {
        case end:
          return 'red'

        case start:
          return 'green'

        default:
          return 'primary'
      }
    },

    deleteQuestions (selected) {
      // Delete Them from Arrays
      this.oSurvey.questions = this.oSurvey.questions.filter(function (x) {
        return selected.indexOf(x) < 0
      })

      this.oSurveyOld.questions = this.oSurveyOld.questions.filter(function (x) {
        return selected.indexOf(x) < 0
      })

      // Push into delete_questions
      this.aDeleteQuestionsIds = this.getIdsFromObject(
        this.copyObject(selected)
      )

      // Empty Selected
      this.selected = []

      // Reorder Questions
      this.reorderQuestions()

      // Close Dialog
      this.bDeleteQuestionDialog = false
    },

    deleteOptions (question, selected) {
      // Delete Them from Arrays
      question.options = question.options.filter(function (x) {
        return selected.indexOf(x) < 0
      })

      // Push into delete_questions
      this.aDeleteOptionsIds = this.getIdsFromObject(this.copyObject(selected))

      // Empty Selected
      this.aSelectedOptions = []

      // Reorder Questions
      this.reorderOptions(question)

      // Close Dialog
      this.bDeleteOptionDialog = false
    },

    move (oMovingElement, aList, iDir) {
      var key1 = this.getPositionByOrder(oMovingElement.order, aList)
      var key2 = key1 + iDir
      console.log(key1, key2)
      var oElement1 = oMovingElement
      var oElement2 = aList[key2]

      if (oElement2) {
        var iTmp = parseInt(oElement1.order)

        oElement1.order = parseInt(oElement2.order)
        oElement2.order = parseInt(iTmp)
      }

      aList.sort((a, b) => (a.order > b.order ? 1 : -1))
    },

    sortSelectedByOrder () {
      this.selected.sort((a, b) => (a.order > b.order ? 1 : -1))
    },

    moveSelectedUp () {
      this.sortSelectedByOrder();
      for (var i = 0; i < this.selected.length; i++) {
        this.moveUp(this.selected[i], this.oSurvey.questions)
      }
    },

    moveSelectedDown () {
      this.sortSelectedByOrder();
      for (var i = this.selected.length - 1; i >= 0; i--) {
        this.moveDown(this.selected[i], this.oSurvey.questions)
      }
    },

    moveUp (oElement1, aList) {
      this.move(oElement1, aList, -1)
    },

    moveDown(oElement1, aList) {
      this.move(oElement1, aList, 1)
    },

    getPositionById(oItem, oObject) {
      return oObject
        .map(function (x) {
          return x.id
        })
        .indexOf(oItem.id)
    },

    getPositionByOrder (iOrder, oObject) {
      return oObject
        .map(function (x) {
          return x.order
        })
        .indexOf(iOrder)
    },

    getRandomId () {
      return parseInt(Date.now() + Math.random() * 1000)
    },

    addOption (o, question) {
      var aO = question.options
      o.order = aO.length + 1
      aO.push(o)
      return o
    },

    addNewOption (question) {
      return this.addOption(
        {
          id: this.getRandomId(),
          color: "#C6C6C6"
        },
        question
      )
    },

    duplicateOption (option, question) {
      if (question && option) {
        var oNewOption = this.copyObject(option);

        // Delete oNewOption.id;
        oNewOption.id = this.getRandomId();

        return this.addOption(oNewOption, question);
      }
    },

    duplicateLastOption(question) {
      var oLast = question.options[question.options.length - 1];
      this.duplicateOption(oLast, question);
    },

    // QUESTIONS
    addQuestion (q) {
      var aQ = this.oSurvey.questions;

      // Increment one order-point up
      q.order = aQ.length + 1
      q.is_new = true

      // Push to other Questions
      aQ.push(q)

      // Return Question
      return q
    },

    addNewQuestion () {
      return this.addQuestion({
        id: this.getRandomId(),
        options: []
      })
    },

    duplicateQuestion (question) {
      if (question) {
        var oNewQ = this.copyObject(question)

        // Delete oNewQ.id;
        oNewQ.id = this.getRandomId()

        // Delete oNewQ.options.id
        oNewQ.options.forEach(option => {
          option.question_id = oNewQ.id
        });

        return this.addQuestion(oNewQ)
      }
    },

    duplicateSelectedQuestions () {
      var aNewSelected = []
      this.selected.forEach(question => {
        aNewSelected.push(this.duplicateQuestion(question))
      });
      this.selected = aNewSelected
      console.log(aNewSelected)
    },

    duplicateLastQuestion () {
      var aQ = this.oSurvey.questions
      var oLastQ = aQ[aQ.length - 1]
      this.duplicateQuestion(oLastQ)
    },

    changeTab (num) {
      window.location.hash = num
    },

    checkTabForHash () {
      if (window.location.hash) {
        var tab = window.location.hash.substr(1)
        tab ? (this.active_tab = tab) : undefined
      }
    },

    surveyFormIsValid () {
      return this.valid
    },

    getIdsFromObject (obj) {
      return obj.map(function (e) {
        return e.id
      })
    },

    surveyFormIsInvalid () {
      return !this.surveyFormIsValid()
    },

    surveyIsEditable () {
      return (this.oSurvey.is_editable && this.bEdit) || this.bCreate
    },

    surveyIsUneditable () {
      return !this.surveyIsEditable()
    },

    getMaxEndDate (date) {
      return this.getMinMaxDate(date, 'end')
    },

    getMinStartDate (date) {
      return this.getMinMaxDate(date, 'start')
    },

    getMinMaxDate (date, type) {
      var d1 = this.getDate(date)
      var active = this.oSurvey.active

      switch (type) {
        case 'start':
          if (this.oSurvey.end_datetime) {
            var end = this.getDate(this.oSurvey.end_datetime)
            return d1 <= end && (active ? this.sTodayDate <= d1 : true)
          }
          break

        case 'end':
          var start = this.getDate(this.oSurvey.start_datetime)
          return d1 >= start
      }

      return active ? this.sTodayDate <= d1 : true
    },

    getDate (ts) {
      return moment(ts).format('YYYY-MM-DD')
    },

    getMinStartTime (hr, min, sec) {
      return this.getMinMaxTime(hr, min, sec, 'start')
    },

    getMaxEndTime (hr, min, sec) {
      return this.getMinMaxTime(hr, min, sec, 'end')
    },

    getMinMaxTime (hr, min, sec, type) {
      if (this.oSurvey.start_datetime && this.oSurvey.end_datetime) {
        var startDate = this.getDate(this.oSurvey.start_datetime)
        var endDate = this.getDate(this.oSurvey.end_datetime)
        if (startDate == endDate) {
          var endDatetime = moment(this.oSurvey.end_datetime).format(
            'YYYY-MM-DD HH:mm:ss'
          )
          var startDatetime = moment(this.oSurvey.start_datetime).format(
            'YYYY-MM-DD HH:mm:ss'
          )

          // SelTime
          var selTime = moment(this.oSurvey.start_datetime).toDate()
          if (hr) selTime.setHours(hr)
          if (min) selTime.setMinutes(min)
          if (sec) selTime.setSeconds(sec)

          // Format again
          selTime = moment(selTime).format('YYYY-MM-DD HH:mm:ss')

          switch (type) {
            case 'start':
              return endDatetime > selTime

            case 'end':
              return startDatetime < selTime
          }
        }
      }

      return true
    },

    getDiffDatetime () {
      if (
        !this.oSurvey ||
        !this.oSurvey.start_datetime ||
        !this.oSurvey.end_datetime
      ) {
        return 0
      }

      var t1 = moment(this.oSurvey.start_datetime)
      var t2 = moment(this.oSurvey.end_datetime)

      return Math.abs(t1 - t2) / 1000
    },

    getDiffDatetimeColor () {
      var delta = this.getDiffDatetime()
      var iDays = Math.floor(delta / 86400)

      switch (true) {
        case iDays > 14:
          return 'positive'

        case iDays > 7:
          return 'grey'

        case iDays > 3:
          return 'warning'

        default:
          return 'negative'
      }

      return 'grey'
    },

    getDiffDatetimeLabel() {
      // get total seconds between the times
      var delta = this.getDiffDatetime();

      if (!delta) return "";

      // calculate (and subtract) whole days
      var iDays = Math.floor(delta / 86400);
      var sDays = "";
      delta -= iDays * 86400;
      if (iDays) sDays = iDays + " " + this.$t("days") + " ";

      // calculate (and subtract) whole hours
      var iHours = Math.floor(delta / 3600) % 24;
      var sHours = "";
      delta -= iHours * 3600;
      if (iHours) sHours = iHours + " " + this.$t("hours") + " ";

      // calculate (and subtract) whole minutes
      var iMinutes = Math.floor(delta / 60) % 60;
      var sMinutes = "";
      delta -= iMinutes * 60;
      if (iMinutes) sMinutes = iMinutes + " " + this.$t("minutes") + " ";

      return sDays + sHours + sMinutes;
    },

    isSameDay(dates) {
      if (dates && dates[0] == dates[1]) {
        return true;
      }
      return false;
    },

    // getStartTimeMax () {
    //   var dates = this.aDates;
    //   var sEndTime = this.oTimes.sEndTime;

    //   if(this.isSameDay(dates) && sEndTime)
    //   {
    //       return sEndTime+':00';
    //   }
    // },

    // getEndTimeMin () {
    //   var dates = this.aDates;

    //   if(this.isSameDay(dates))
    //   {
    //     return this.oTimes.sStartTime+':00';
    //   }
    // },

    // updateDatetimes() {
    //   if(this.oSurvey) {

    //     var d1 = (this.aDates[0]) ? this.aDates[0].substr(0, 10) : '';
    //     var d2 = (this.aDates[1]) ? this.aDates[1].substr(0, 10) : '';

    //     var t1 = (this.oTimes.sStartTime) ? ' ' + this.oTimes.sStartTime + ':00'   : '';
    //     var t2 = (this.oTimes.sEndTime)   ? ' ' + this.oTimes.sEndTime   + ':00'   : '';

    //     var tmp = '';

    //     if(d1 > d2) {
    //       tmp = d2;
    //       d2 = d1;
    //       d1 = tmp;
    //     }

    //     this.oSurvey.start_datetime = d1 + t1 ;
    //     this.oSurvey.end_datetime   = d2 + t2 ;
    //   }
    // },

    getDatesDiffDays() {
      return this.getDiffDays(
        this.oSurvey.start_datetime,
        this.oSurvey.end_datetime
      );
    },

    getDiffDays(d1, d2) {
      var date1 = new Date(d1);
      var date2 = new Date(d2);
      var diff_in_time = date2.getTime() - date1.getTime();
      var diff_in_days = diff_in_time / (1000 * 3600 * 24);

      return (Math.abs(diff_in_days) + 1).toString();
    },

    formatDate(sDate) {
      var locale = "";
      var dDate = new Date(sDate);

      switch (this._i18n.locale) {
        case "de":
          locale = "de-DE";
          break;

        default:
          locale = this._i18n.fallbackLocale;
          break;
      }

      if (sDate && dDate) {
        return dDate.toLocaleDateString(locale);
      }
    },

    formatTime(sDate) {
      return sDate ? sDate.substr(11, 5) + " Uhr" : "";
    },

    showSnackbarError: function(text) {
      var _this = this;
      _this.$q.notify({
        message: _this.$t(text),
        color: "red",
        position: "top-right",
        actions: [{ icon: "close", color: "white" }],
        timeout: 6000
      });
    },

    showSnackbarSuccess: function(text) {
      var _this = this;
      _this.$q.notify({
        message: _this.$t(text),
        color: "green",
        position: "top-right",
        actions: [{ icon: "close", color: "white" }],
        timeout: 3000
      });
    },

    startEditMode: function() {
      this.bCreate = false;
      this.bEdit = true;
    },

    startCreateMode: function() {
      this.bCreate = true;
      this.bEdit = false;

      var tmp = {
        active: 1,
        only_editable_by_creator: 1,
        questions: []
      };

      if (!this.oSurvey) this.oSurvey = this.copyObject(tmp);
      if (!this.oSurveyOld) this.oSurveyOld = this.copyObject(tmp);
    },

    copyObject(obj) {
      if (typeof obj != "undefined") {
        var copy = JSON.parse(JSON.stringify(obj));
        if (copy) return copy;
      }
    },

    isSaved() {
      var bReturn = true;
      var itemL = this.copyObject(this.oSurvey);
      var itemR = this.copyObject(this.oSurveyOld);

      if (itemL && itemR) {
        // Differences
        if (JSON.stringify(itemR) != JSON.stringify(itemL)) {
          // console.log(JSON.stringify(itemR), JSON.stringify(itemL));
          bReturn = false;
        } else {
          bReturn = true;
        }
      }
      return bReturn;
    },

    isUnsaved() {
      return !this.isSaved();
    },

    updateSurvey: function() {
      var _t = this;
      this.bIsLoading = true;

      // Update Users
      this.$store
        .dispatch("surveys/updateSurvey", {
          survey: _t.oSurvey,
          delete_questions_ids: _t.aDeleteQuestionsIds,
          delete_options_ids: _t.aDeleteOptionsIds
        })
        .then(function(e) {
          // Success
          if (!e || !e.response || !e.response.data || !e.response.data.error) {
            _t.showSnackbarSuccess(_t.$t("data_saved"));
            _t.oSurvey = _t.copyObject(e.data);
            _t.oSurveyOld = _t.copyObject(e.data);

            if (_t.bCreate) {
              _t.$router.push({
                name: "backend.survey",
                params: {
                  id: _t.oSurvey.id
                }
              });
            }

            _t.aDeleteQuestionsIds = [];
            _t.aDeleteOptionsIds = [];

            _t.startEditMode();
          }
          _t.bIsLoading = false;
        })
        .catch(function(e) {
          console.log(e);
          // Error
          if (e.reponse && e.reponse.data && e.response.data.error) {
            var errText = "";
            for (var e in e.response.data.error) {
              errText += ": " + err[e];
            }
          }

          _t.showSnackbarError(_t.$t("data_unsaved") + "<br />" + errText);
          _t.bIsLoading = false;
        });
    }
  }
};
</script>

<style lang="scss">
// Display Events as Long Stripes
.q-date__event {
  width: 37px;
  border-radius: 0;
  bottom: 1px;
  height: 4px;
}
</style>
