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
      <br>
      <br>

      <UserDataModal
        s-icon="group"
        :b-outline="false"
        s-create-text="Neue Gruppe erstellen"
        s-edit-text="Gruppen Bearbeiten"
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

      <br>
      <br>
    </template>

    <div v-if="oSurvey">
      <!-- Data Sheet -->
      <template>
        <q-form ref="form" v-model="valid" style="max-width: 1280px;" @submit.prevent>
          <q-toolbar color="primary" dark>
            <q-toolbar-title>
              <template v-if="bCreate">
                {{ "Neue Umfrage erstellen" }}
              </template>
              <template v-if="bEdit && oSurvey">
                {{ 'Umfrage' }} #{{ oSurvey.id }}
              </template>
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
            <q-tab name="basis" label="Basis" icon="settings" @click="changeTab('basis')" />
            <q-tab
              name="fragen"
              label="Fragen"
              icon="question_answer"
              @click="changeTab('fragen')"
            />
            <q-tab name="gruppen" label="Gruppen" icon="group" @click="changeTab('gruppen')" />
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
                      v-model="oSurvey.title"
                      :disable="surveyIsUneditable()"
                      dense
                      persistent-hint
                      outlined
                      hint="Titel der Umfrage. Wird angezeigt"
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
                      v-model="oSurvey.author"
                      :disable="surveyIsUneditable()"
                      dense
                      persistent-hint
                      outlined
                      hint="Author in Textform. Wird angezeigt"
                      :placeholder="oSurveyOld.author ? oSurveyOld.author : 'z.B.: &quot;Dr. Johannes Müller&quot; '"
                      label="Autor"
                      required
                    />
                  </q-item-section>
                </q-item>

                <!-- Description Short -->
                <q-item>
                  <q-item-section>
                    <q-input
                      v-model="oSurvey.desc_short"
                      :disable="surveyIsUneditable()"
                      dense
                      persistent-hint
                      outlined
                      hint="Kurze Beschreibung der Umfrage"
                      label="Kurzbeschreibung"
                      required
                    />
                  </q-item-section>
                </q-item>

                <!-- Description Long -->
                <q-item>
                  <q-item-section>
                    <HtmlEditor
                      v-if="oSurvey.use_html"
                      :model="oSurvey"
                      field="desc_long"
                      :disable="surveyIsUneditable()"
                    />
                    <q-input
                      v-else
                      v-model="oSurvey.desc_long"
                      type="textarea"
                      :disable="surveyIsUneditable()"
                      outlined
                      dense
                      persistent-hint
                      hint="Lange Beschreibung der Umfrage"
                      label="Langbeschreibung"
                      required
                    />
                  </q-item-section>
                </q-item>

                <br>

                <q-item-label>Konfigurations-Einstellungen</q-item-label>

                <q-item>
                  <q-item-section side top>
                    <q-checkbox
                      v-model="oSurvey.active"
                      :disable="surveyIsUneditable()"
                      color="primary"
                      :true-value="1"
                      :false-value="0"
                    />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Aktiviert</q-item-label>
                    <q-item-label
                      caption
                    >
                      Ist diese Umfrage ausfüllbar (aktiviert) oder nicht ausfüllbar. Standard ist aktiviert.
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section side top>
                    <q-checkbox
                      v-model="oSurvey.only_editable_by_creator"
                      :disable="surveyIsUneditable()"
                      color="primary"
                      :true-value="1"
                      :false-value="0"
                    />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Nur für Ersteller Editierbar</q-item-label>
                    <q-item-label
                      caption
                    >
                      Nur Sie selbst als Ersteller können diese Umfrage editieren. Im Standard sind Umfragen nur für Sie selbst editierbar
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section side top>
                    <q-checkbox
                      v-model="oSurvey.is_public"
                      :disable="surveyIsUneditable()"
                      color="red"
                      :true-value="1"
                      :false-value="0"
                    />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Öffentlich</q-item-label>
                    <q-item-label
                      caption
                    >
                      Diese Umfrage ist öffentlich verfügbar. Im Standard sind Umfragen nicht öffentlich
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section side top>
                    <q-checkbox
                      :value="oSurvey.use_html || 0"
                      :disable="surveyIsUneditable()"
                      color="orange"
                      :true-value="1"
                      :false-value="0"
                      @input="changeUseHtml"
                    />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>HTML</q-item-label>
                    <q-item-label
                      caption
                    >
                      Benutze HTML Inhalte bei Beischreibungstexten. Warnung!
                    </q-item-label>
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
                      <q-item-label caption>
                        Zeige erweiterte Einstellungen
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <template v-if="bExtendedSettings">
                    <q-item>
                      <q-item-section side top>
                        <q-checkbox
                          v-model="oSurvey.is_finished"
                          color="red"
                          :true-value="1"
                          :false-value="0"
                          :disable="surveyIsUneditable()"
                        />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Beendet</q-item-label>
                        <q-item-label caption>
                          Die Umfrage wurde Beendet. Nicht umkehrbar
                        </q-item-label>
                      </q-item-section>
                    </q-item>

                    <q-item>
                      <q-item-section side top>
                        <q-checkbox
                          v-model="oSurvey.is_canceled"
                          :disable="surveyIsUneditable()"
                          color="red"
                          :true-value="1"
                          :false-value="0"
                        />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Abgebrochen</q-item-label>
                        <q-item-label caption>
                          Die Umfrage wurde abgebrochen. Nicht umkehrbar
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </template>
                </template>

                <q-item-label header>
                  Datums-Einstellungen
                </q-item-label>

                <!-- Dates-->
                <q-item class="row q-gutter-md">
                  <div class="col col-12 col-sm-6 col-md-6">
                    <q-card>
                      <q-card-section>
                        <div class="text-h6">
                          Beginn und Ende anpassen
                        </div>
                      </q-card-section>

                      <q-card-section>
                        <p>Sobald ihre Umfrage beginnt - können Sie diese nicht mehr anpassen</p>

                        <div class="row">
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              :value="getDateFormat(oSurvey.start_datetime)"
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
                                  :mask="datepicker_mask"
                                >
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn
                                      v-close-popup
                                      :label="$t('closer_button')"
                                      color="primary"
                                      flat
                                    />
                                  </div>
                                </q-date>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              v-if="oSurvey.start_datetime"
                              :value="getTimeFormat(oSurvey.start_datetime)"
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
                                  v-model="oSurvey.start_datetime"
                                  format24h
                                  :disable="surveyIsUneditable()"
                                  :options="getMinStartTime"
                                  :mask="datepicker_mask"
                                  color="green"
                                >
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn
                                      v-close-popup
                                      :label="$t('closer_button')"
                                      color="primary"
                                      flat
                                    />
                                  </div>
                                </q-time>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                        </div>

                        <div v-if="oSurvey.start_datetime" class="row">
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              :value="getDateFormat(oSurvey.end_datetime)"
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
                                  :mask="datepicker_mask"
                                >
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn
                                      v-close-popup
                                      :label="$t('closer_button')"
                                      color="primary"
                                      flat
                                    />
                                  </div>
                                </q-date>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              :value="getTimeFormat(oSurvey.end_datetime)"
                              :disable="surveyIsUneditable()"
                              label="Endet um"
                              readonly
                              :rules="required"
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
                                  v-model="oSurvey.end_datetime"
                                  format24h
                                  :disable="surveyIsUneditable()"
                                  :mask="datepicker_mask"
                                  color="grey"
                                  :options="getMaxEndTime"
                                >
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn
                                      v-close-popup
                                      :label="$t('closer_button')"
                                      color="primary"
                                      flat
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
                              :text="['Hallo! Die Umfrage ersteckt sich über folgende Zeit: <br/><strong>'+ getDiffDatetimeLabel() + '</strong>. Startend am ' + getDateFormat(oSurvey.start_datetime) + ' bis zum ' + getDateFormat(oSurvey.end_datetime) ]"
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
                      :value="oSurvey.start_datetime || sTodayDatetime"
                      :mask="datepicker_mask"
                      :events="events"
                      :event-color="eventColor"
                      :options="events"
                      minimal
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
                      <div class="flex-grow-1" />
                      <!-- <q-input
                        v-model="sSearch"
                        style="max-width: 400px;"
                        :label="$t('Search')"
                        autocomplete="off"
                        append-icon="search"
                        hide-details
                        outlined
                      /> -->
                      <q-input
                        v-model="pagination.rowsPerPage"
                        number
                        type="number"
                        hide-details
                        style="max-width: 150px;"
                        label="Fragen pro Seite"
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
                      <q-btn :label="selected.length + ' ' + $t('edit')" unelevated rounded icon="mdi-pencil">
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
                      <div class="flex-grow-1" />
                    </q-toolbar>

                    <!-- Delete - Dialog -->
                    <q-dialog
                      v-model="bDeleteQuestionDialog"
                      max-width="500"
                      dark
                      content-class="naked dark centered"
                    >
                      <h2 class="display-2">
                        Fragen Löschen?
                      </h2>
                      <p>Möchten Sie {{ selected.length }} Fragen löschen?</p>
                      <div class="container fluid">
                        <div class="row" align="center">
                          <div class="col text-center" cols="12" sm="12">
                            <q-btn depressed outlined @click="bDeleteQuestionDialog = false">
                              Abbruch
                            </q-btn>
                            <q-btn
                              depressed
                              color="error"
                              @click.prevent="deleteQuestions(selected)"
                            >
                              Löschen
                            </q-btn>
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
                          <q-td auto-width>
                            <q-checkbox
                              v-model="props.selected"
                              dense
                            />
                          </q-td>

                          <q-td key="order" :props="props">
                            <div style="white-space: nowrap;">
                              <q-icon
                                name="mdi-arrow-up"
                                x-small
                                class="pointer"
                                @click="moveUp(props.row, oSurvey.questions)"
                              />
                              {{ props.row.order }}
                              <q-icon
                                name="mdi-arrow-down"
                                x-small
                                class="pointer"
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

                          <!-- ID -->
                          <!-- <q-td auto-width>
                            <span>
                              <small style="word-break: break-all">{{ props.row.id }}</small>
                            </span>
                          </q-td> -->

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
                                    <div class="col col-12 col-sm-6 col-md-6">
                                      <q-list subheader two-line flat>
                                        <q-item-label header>
                                          Allgemeine Frage-Einstellungen für Frage-ID: #{{ props.row.id }}
                                        </q-item-label>
                                        <q-item>
                                          <q-item-section>
                                            <q-btn color="primary" label="Vorlage laden" @click="askLoadPreset(props.row)" />
                                          </q-item-section>
                                        </q-item>

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
                                            <HtmlEditor
                                              v-if="oSurvey.use_html"
                                              :model="props.row"
                                              field="description"
                                              :disable="surveyIsUneditable()"
                                            />
                                            <q-input
                                              v-else
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

                                    <div class="col col-12 col-sm-6 col-md-6">
                                      <q-list subheader two-line flat>
                                        <q-item-label header>
                                          Kommentar-Einstellungen
                                        </q-item-label>

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
                                                :disable="(surveyIsUneditable() && props.row.is_commentable) ? true : false"
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
                                                    >
                                                      Wieviele einzelne Zeichen darf ein Kommentar maximal haben?
                                                    </q-tooltip>
                                                  </q-icon>
                                                </template>
                                              </q-input>
                                            </q-item-section>
                                          </q-item>
                                        </template>
                                      </q-list>
                                      <q-list subheader two-line flat>
                                        <q-item-label header>
                                          Frage-Optionen
                                        </q-item-label>
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
                                                      >
                                                        Minimal wählbare Optionen
                                                      </q-tooltip>
                                                    </q-icon>
                                                  </template>
                                                </q-input>
                                              </div>
                                              <div class="col" xl="3" md="3" sm="6" xs="12">
                                                <q-input
                                                  v-model="props.row.max_options"
                                                  :disable="surveyIsUneditable()"
                                                  dense
                                                  outlined
                                                  placeholder="1 - 255"
                                                  type="number"
                                                  :min="parseInt(props.row.min_options)"
                                                  label="Maximale Optionen"
                                                  required
                                                >
                                                  <template v-slot:append>
                                                    <q-icon :name="help_icon">
                                                      <q-tooltip
                                                        self="center middle"
                                                      >
                                                        Maximale wählbare Optionen
                                                      </q-tooltip>
                                                    </q-icon>
                                                  </template>
                                                </q-input>
                                              </div>
                                            </div>
                                          </q-item-section>
                                        </q-item>
                                        <q-item>
                                          <q-item-section>
                                            <q-select
                                              v-model="props.row.format"
                                              outlined
                                              :options="question_formats"
                                              option-value="id"
                                              option-label="label"
                                              label="Darstellungs-Format (für Frage-Optionen)"
                                              emit-value
                                              map-options
                                              required
                                              :error="!props.row.format"
                                            >
                                              <template v-slot:selected-item="scope">
                                                <q-item
                                                  v-bind="scope.itemProps"
                                                  v-on="scope.itemEvents"
                                                >
                                                  <q-item-section avatar>
                                                    <q-icon :name="scope.opt.icon" />
                                                  </q-item-section>
                                                  <q-item-section>
                                                    <q-item-label v-html="scope.opt.label" />
                                                    <q-item-label caption>
                                                      {{ scope.opt.description }}
                                                    </q-item-label>
                                                  </q-item-section>
                                                </q-item>
                                              </template>

                                              <template v-slot:option="scope">
                                                <q-item
                                                  v-bind="scope.itemProps"
                                                  v-on="scope.itemEvents"
                                                >
                                                  <q-item-section avatar>
                                                    <q-icon :name="scope.opt.icon" />
                                                  </q-item-section>
                                                  <q-item-section>
                                                    <q-item-label v-html="scope.opt.label" />
                                                    <q-item-label caption>
                                                      {{ scope.opt.description }}
                                                    </q-item-label>
                                                  </q-item-section>
                                                </q-item>
                                              </template>
                                            </q-select>
                                          </q-item-section>
                                        </q-item>
                                      </q-list>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col" xl="12" sm="12" xs="12">
                                      <q-list subheader two-line flat>
                                        <q-item-label header>
                                          Optionen:
                                        </q-item-label>
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
                                                height="auto"
                                              >
                                                <span>
                                                  <q-btn label="Löschen" icon="delete" flat rounded unelevated />
                                                  <q-popup-edit
                                                    single-line
                                                    persistent
                                                    :cover="false"
                                                    self="center left"
                                                    anchor="center right"
                                                    :offset="[5, 0]"
                                                    value="1"
                                                    @save="save(option.row)"
                                                  >
                                                    <div class="row" align="center">
                                                      <div class="col text-center" cols="12" sm="12">
                                                        Möchten Sie wirklich {{ aSelectedOptions.length }} Optionen löschen?
                                                        <q-btn
                                                          v-close-popup
                                                          depressed
                                                          color="grey"
                                                          outlined
                                                          flat
                                                          size="sm"
                                                        >Abbruch</q-btn>
                                                        <q-btn
                                                          v-close-popup
                                                          depressed
                                                          flat
                                                          color="red"
                                                          size="sm"
                                                          @click="deleteOptions(aSelectedOptions)"
                                                        >Löschen</q-btn>
                                                      </div>
                                                    </div>
                                                  </q-popup-edit>
                                                </span>
                                              </q-toolbar>

                                              <!-- No Selected Toolbar -->
                                              <q-toolbar
                                                v-else
                                                class="coha--toolbar"
                                                :flat="sSearch == ''"
                                                floating
                                                min-height="85px"
                                                height="auto"
                                              >
                                                <div class="flex-grow-1" />
                                              </q-toolbar>

                                              <q-table
                                                v-model="aSelectedOptions"
                                                :selected.sync="aSelectedOptions"
                                                :columns="aOptionHeaders"
                                                :data="props.row.options"
                                                dense
                                                multi-sort
                                                show-select
                                                selection="multiple"
                                                :search="sOptionSearch"
                                                :items-per-page="parseInt(iOptionsPerPage)"
                                                :sort-by="['order']"
                                                :sort-desc="[false]"
                                                :footer-props="{
                                                  showFirstLastPage: true,
                                                }"
                                                :pagination.sync="oOptionsPagination"
                                                class="my-data-table f-height options-table"
                                              >
                                                <template v-slot:header-cell-order="props">
                                                  <q-th :props="props" class="should-be-sorted">
                                                    <span>{{ props.col.label }}</span>
                                                    <q-icon name="sort" size="1.5em" />
                                                  </q-th>
                                                </template>

                                                <template v-slot:header-cell-title="props">
                                                  <q-th :props="props">
                                                    {{ props.col.label }}
                                                    <q-icon name="title" size="1.5em" />
                                                  </q-th>
                                                </template>

                                                <template v-slot:header-cell-subtitle="props">
                                                  <q-th :props="props">
                                                    {{ props.col.label }}
                                                    <q-icon name="text_fields" size="1.5em" />
                                                  </q-th>
                                                </template>

                                                <template v-slot:header-cell-description="props">
                                                  <q-th :props="props">
                                                    <span>{{ props.col.label }}</span>
                                                    <q-icon name="message_text" size="1.5em" style="display: inline-block" />
                                                  </q-th>
                                                </template>

                                                <template v-slot:header-cell-color="props">
                                                  <q-th :props="props">
                                                    {{ props.col.label }}
                                                    <q-icon name="format_color_fill" size="1.5em" />
                                                  </q-th>
                                                </template>

                                                <template v-slot:header-cell-value="props">
                                                  <q-th :props="props">
                                                    {{ props.col.label }}
                                                    <q-icon name="perm_data_setting" size="1.5em">
                                                      <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                                                        <strong>{{ props.col.label }}</strong> <br>
                                                        (Definiert wieviel die Option gewerted wird.<br>
                                                        Beispiele für Werte wären bei Mittelmäßig 0, <br>
                                                        bei etwas schlechter -4 und bei sehr gut 8)
                                                      </q-tooltip>
                                                    </q-icon>
                                                  </q-th>
                                                </template>

                                                <!-- Body Slots -->
                                                <template v-slot:body="option">
                                                  <q-tr :props="option">
                                                    <!-- Selected -->
                                                    <q-td auto-width>
                                                      <q-checkbox
                                                        v-model="option.selected"
                                                        dense
                                                      />
                                                    </q-td>

                                                    <!-- Order -->
                                                    <q-td>
                                                      <div style="white-space: nowrap;">
                                                        <!-- <q-icon name="mdi-arrow-up" x-small @click="moveUp(props.item, item.options)" /> -->
                                                        <q-icon name="mdi-arrow-up" x-small class="pointer" @click="moveUp(option.row, props.row.options)" />
                                                        {{ option.row.order }}
                                                        <q-icon name="mdi-arrow-down" x-small class="pointer" @click="moveDown(option.row, props.row.options)" />
                                                      </div>
                                                    </q-td>

                                                    <!-- Value -->
                                                    <q-td>
                                                      {{ option.row.value }}
                                                      <q-popup-edit
                                                        v-model="option.row.value"
                                                        buttons
                                                        single-line
                                                        persistent
                                                        :cover="false"
                                                        self="center left"
                                                        anchor="center right"
                                                        :offset="[5, 0]"
                                                        @save="save(option.row)"
                                                      >
                                                        <q-input
                                                          v-model="option.row.value"
                                                          :label="$t('value')"
                                                          autofocus
                                                          type="number"
                                                        />
                                                      </q-popup-edit>
                                                    </q-td>

                                                    <!-- Title -->
                                                    <q-td>
                                                      <b>{{ option.row.title }}</b>
                                                      <q-popup-edit
                                                        v-model="option.row.title"
                                                        buttons
                                                        single-line
                                                        persistent
                                                        :cover="false"
                                                        self="center left"
                                                        anchor="center right"
                                                        :offset="[5, 0]"
                                                        @save="save(option.row)"
                                                      >
                                                        <q-input
                                                          v-model="option.row.title"
                                                          :label="$t('title')"
                                                          autofocus
                                                        />
                                                      </q-popup-edit>
                                                    </q-td>

                                                    <!-- Subtitle -->
                                                    <q-td>
                                                      {{ option.row.subtitle }}
                                                      <q-popup-edit
                                                        v-model="option.row.subtitle"
                                                        buttons
                                                        single-line
                                                        persistent
                                                        :cover="false"
                                                        self="center left"
                                                        anchor="center right"
                                                        :offset="[5, 0]"
                                                        @save="save(option.row)"
                                                      >
                                                        <q-input
                                                          v-model="option.row.subtitle"
                                                          :label="$t('subtitle')"
                                                          autofocus
                                                        />
                                                      </q-popup-edit>
                                                    </q-td>

                                                    <!-- Description -->
                                                    <q-td>
                                                      {{ option.row.description }}
                                                      <q-popup-edit
                                                        v-model="option.row.description"
                                                        buttons
                                                        single-line
                                                        persistent
                                                        :cover="false"
                                                        self="center left"
                                                        anchor="center right"
                                                        :offset="[5, 0]"
                                                        @save="save(option.row)"
                                                      >
                                                        <q-input
                                                          v-model="option.row.description"
                                                          :label="$t('description')"
                                                          autofocus
                                                          type="textarea"
                                                        />
                                                      </q-popup-edit>
                                                    </q-td>

                                                    <!-- Color -->
                                                    <q-td>
                                                      <q-btn
                                                        class="c-code-text"
                                                        unelevated
                                                        rounded
                                                        size="md"
                                                        :style="'text-transform: uppercase; background-color: ' + option.row.color"
                                                      >
                                                        <span :class="getNegativeColor(option.row.color)">
                                                          {{ option.row.color || 'no color' }}
                                                        </span>
                                                        <q-popup-proxy transition-show="scale" transition-hide="scale">
                                                          <q-color
                                                            v-model="option.row.color"
                                                            :palette="colorPalette"
                                                            default-view="palette"
                                                          />
                                                        </q-popup-proxy>
                                                      </q-btn>
                                                      <q-btn v-if="option.row.color" size="sm" icon="clear" rounded flat round @click="option.row.color = ''" />
                                                    </q-td>

                                                    <!-- ID -->
                                                    <!-- <q-td auto-width>
                                                      <span>
                                                        <small style="word-break: break-all">{{ option.row.id }}</small>
                                                      </span>
                                                    </q-td> -->

                                                    <!-- Actions -->
                                                    <q-td auto-width>
                                                      <!-- Delete - Dialog -->
                                                      <q-btn
                                                        unelevated
                                                        round
                                                        outlined
                                                        color="red"
                                                        dark
                                                        icon="delete"
                                                        size="sm"
                                                      />
                                                      <q-popup-edit
                                                        value="1"
                                                        single-line
                                                        persistent
                                                        :cover="false"
                                                        self="center left"
                                                        anchor="center right"
                                                        :offset="[5, 0]"
                                                        @save="save(option.row)"
                                                      >
                                                        <div class="row" align="center">
                                                          <div class="col text-center" cols="12" sm="12">
                                                            Möchten Sie diese Option löschen?
                                                            <q-btn
                                                              v-close-popup
                                                              depressed
                                                              color="grey"
                                                              outlined
                                                              size="sm"
                                                            >
                                                              Abbruch
                                                            </q-btn>
                                                            <q-btn
                                                              v-close-popup
                                                              depressed
                                                              color="red"
                                                              size="sm"
                                                              @click.prevent="deleteOption(option.row, props.row)"
                                                            >
                                                              Löschen
                                                            </q-btn>
                                                          </div>
                                                        </div>
                                                      </q-popup-edit>
                                                    </q-td>
                                                  </q-tr>
                                                </template>
                                                <template v-slot:bottom-row>
                                                  <q-td colspan="100%">
                                                    <q-btn
                                                      label="Neue Option hinzufügen"
                                                      color="primary"
                                                      icon="plus_one"
                                                      unelevated
                                                      rounded
                                                      outline
                                                      @click="addNewOption(props.row)"
                                                    />
                                                    &nbsp;
                                                    &nbsp;
                                                    <q-btn
                                                      label="Letzte Option duplizieren"
                                                      color="primary"
                                                      icon="control_point_duplicate"
                                                      unelevated
                                                      rounded
                                                      outlined
                                                      @click="duplicateLastOption(props.row)"
                                                    />
                                                  </q-td>
                                                </template>
                                              </q-table>
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
                  <q-btn label="Neue Frage hinzufügen" icon="plus_one" @click="addNewQuestion()" />&nbsp; &nbsp;
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

              <q-btn
                color="grey"
                dark
                class="mr-4"
                label="Zurück"
                unelevated
                outlined
              />
              &nbsp;
              <q-btn
                v-if="surveyIsEditable()"
                color="primary"
                type="submit"
                class="mr-4 white--text"
                :disabled="surveyFormIsInvalid() || surveyIsInvalid()"
                :label="'Umfrage Speichern ' + (isUnsaved() ? '*' : '')"
                unelevated
                icon="save"
                @click="tryUpdateSurvey"
              />
              &nbsp;
              <!-- <q-btn
                color="primary"
                flat
                round
                icon="settings"
              >
                <q-menu dense>
                  <q-list style="min-width: 100px">
                    <q-item v-close-popup :disabled="surveyIsUneditable() || isUnsaved()" clickable>
                      <q-item-section>
                        Export
                      </q-item-section>
                      <q-item-section avatar>
                        <q-icon name="export" />
                      </q-item-section>
                    </q-item>
                  </q-list>
                </q-menu>
              </q-btn> -->

              <q-page-sticky position="bottom-right" :offset="[18, 18]" style="z-index: 999">
                <q-btn
                  v-show="isUnsaved()"
                  :disable="surveyIsUneditable()"
                  fab
                  icon="mdi-content-save"
                  color="green"
                  size="md"
                  @click="tryUpdateSurvey"
                />
                <q-fab
                  icon="arrow_drop_up"
                  direction="up"
                  color="grey"
                  vertical-actions-align="right"
                >
                  <q-fab-action
                    fab
                    dark
                    small
                    color="secondary"
                    icon="get_app"
                    label="Umfrage als Datei exportieren"
                    label-position="left"
                    external-label
                    @click="exportToFile"
                  />
                  <q-fab-action
                    :disable="surveyIsUneditable()"
                    fab
                    dark
                    small
                    color="warning"
                    icon="publish"
                    label="Umfrage aus Datei importieren"
                    label-position="left"
                    external-label
                    @click="bImportDialog = true"
                  />
                  <q-fab-action
                    :disable="isSaved()"
                    fab
                    dark
                    small
                    color="warning"
                    icon="mdi-restore"
                    label="Umfrage Zurücksetzen"
                    label-position="left"
                    external-label
                    @click="resetSurvey"
                  />
                  <q-fab-action
                    v-if="oSurvey.id"
                    fab
                    dark
                    small
                    color="red"
                    icon="mdi-delete"
                    label="Umfrage Löschen"
                    label-position="left"
                    external-label
                    @click="tryDeleteSurvey"
                  />
                </q-fab>
              </q-page-sticky>
            </q-item>
          </q-list>
        </q-form>
      </template>
    </div>

    <!-- Import Dialog -->
    <q-dialog v-model="bImportDialog" maximized>
      <q-card>
        <q-bar>
          <q-space />
          <q-btn v-close-popup dense flat icon="close">
            <q-tooltip content-class="bg-white text-primary">
              Close
            </q-tooltip>
          </q-btn>
        </q-bar>

        <q-card-section>
          <div class="text-h6">
            Import from File
          </div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <p>
            Kopieren Sie den Inhalt einer survey-***-.json Datei in dieses Textfeld
          </p>
          <br>
          <q-input
            v-model="sImportJson"
            class="c-code-text"
            filled
            type="textarea"
            placeholder="Inhalt von survey-***-.json"
            :error="sImportJson != '' && invalidJson(sImportJson)"
          >
            <template v-slot:error>
              Der Inhalt ist nicht valide. Bitte überprüfen Sie die JSON-Validität des Textes.
            </template>
          </q-input>
          <br>

          <div class="row">
            <div class="col col-12 col-sm-6 col-md-6">
              <q-select
                v-model="oImportType"
                filled
                :options="
                  [
                    {
                      label: 'Überschreiben',
                      value: 'override',
                      description: 'Vorsicht! Alle bisherigen Daten (Titel, Fragen, ...) werden überschrieben und mit den Daten aus der importierten Datei überschrieben!',
                      icon: 'warning'
                    },
                    // {
                    //   label: 'Zusammenfügen Sanft',
                    //   value: 'merge',
                    //   description: 'Merge / Zusammenfügen. Füge dort ein wo Felder leer sind',
                    //   icon: 'call_merge'
                    // },
                    {
                      label: 'Fragen anfügen',
                      value: 'questions_append',
                      description: 'Fragen aus Import-Datei werden zusätzlich zu den aktuellen Fragen hinzugefügt',
                      icon: 'call_merge'
                    }
                  ]
                "
                label="Import-Typ"
                color="primary"
                clearable
                required
                options-selected-class="text-deep-orange"
              >
                <template v-slot:option="scope">
                  <q-item
                    v-bind="scope.itemProps"
                    v-on="scope.itemEvents"
                  >
                    <q-item-section avatar>
                      <q-icon :name="scope.opt.icon" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label v-html="scope.opt.label" />
                      <q-item-label caption>
                        {{ scope.opt.description }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>

              <br>
              <br>

              <q-btn
                v-close-popup
                outline
                label="zurück"
                unelevated
              />
              <q-btn
                v-if="sImportJson"
                :disable="invalidJson(sImportJson) || !oImportType"
                label="Import Survey"
                color="primary"
                unelevated
                @click="importFromFile"
              />
              <br>
            </div>

            <div v-if="sImportJson && validJson(sImportJson)" class="col col-12 col-sm-6 col-md-6">
              Inhalte überprüfen
              <q-scroll-area
                style="min-height: 250px; height: 50vh; max-height: 770px;"
              >
                <span class="import_comment">{{ toJson(sImportJson) }}</span>
              </q-scroll-area>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
// import axios from 'axios'
import { mapGetters } from 'vuex'
import dayjs from 'dayjs'
import UserDataModal from '~/components/BackendUserDataModal'
import HtmlEditor from '~/components/Backend/HtmlEditor'

export default {

  components: {
    UserDataModal,
    HtmlEditor
  },

  data () {
    return {

      // Import Dialog
      bImportDialog: false,
      sImportJson: '',
      oImportType: '',

      // Colors
      colorPalette: [
        '#55a559', // GREEN (success - positive)
        '#dc6161', // RED (error)
        '#cf6035', // red
        '#E6BE72', // warning
        '#cfae35', // orange
        '#D9B801',
        '#C6C6C6', // info
        '#019A9D', '#B2028A',
        '#2A0449', '#019A9D',
        '#54656C', // primary
        '#9AC0C9', // secondary
        '#fafafa', // background
        '#E8D03E' // accent
      ],

      // Questions Extended (local storage)
      questions_extended: [],

      // Question Presets
      question_presets: [
        {
          label: 'Auswählen',
          readonly: true,
          disable: true,
          value: '',
          id: '',
          color: 'secondary'
        },
        {
          label: 'Schulnoten Erweitert (Mit Plus und Minus)',
          value: 'school_notes_advanced',
          id: 'school_notes_advanced',
          load: (_t, question) => {
            return this.loadPresetSchoolNotes(question, '6 5- 5 5+ 4- 4 4+ 3- 3 3+ 2- 2 2+ 1- 1 1+')
          }
        },
        {
          label: 'Schulnoten Einfach',
          value: 'school_notes_simple',
          id: 'school_notes_simple',
          load: (_t, question) => {
            return this.loadPresetSchoolNotes(question, '6 5 4 3 2 1')
          }
        },
        {
          label: '0 bis 10 Slider',
          value: 'slider_zero_to_ten',
          id: 'slider_zero_to_ten',
          load: (_t, question) => {
            return this.loadPresetToTenSlider(question, 0)
          }
        },
        {
          label: '1 bis 10 Slider',
          value: 'slider_one_to_ten',
          id: 'slider_one_to_ten',
          load: (_t, question) => {
            return this.loadPresetToTenSlider(question, 1)
          }
        },
        {
          label: '1 bis 5 Slider',
          value: 'slider_five',
          id: 'slider_five',
          load: (_t, question) => {
            // Change Question
            question.format = 'slider'
            question.min_options = 1
            question.max_options = 1

            // Change Options
            for (let i = 1; i <= 5; i++) {
              var c = '#C6C6C6'
              var v = 0
              var t = i
              var s = ''
              var d = ''

              switch (i) {
                case 1:
                  v = -2
                  c = '#cf8c36'
                  s = 'Garnicht'
                  break

                case 2:
                  v = -1
                  c = '#cfae35'
                  s = 'Eher nicht'
                  break

                case 3:
                  v = 0
                  c = '#c6c6c6'
                  s = 'Mittelmäßig'
                  d = 'Durchschnittlich. 50 / 50'
                  break

                case 4:
                  v = 1
                  c = '#7ebd82'
                  s = 'Gut'
                  break

                case 5:
                  v = 2
                  c = '#55a559'
                  s = 'Sehr gut'
                  break

                default:
                  break
              }

              _t.addOption({
                id: _t.getRandomId(),
                question_id: question.id,
                title: t,
                value: v,
                color: c,
                subtitle: s,
                description: d
              }, question)
            }
          }
        },
        {
          label: '3er Slider',
          value: 'slider_three',
          id: 'slider_three',
          load: (_t, question) => {
            return this.loadPresetToThreeSlider(question)
          }
        },
        {
          label: 'Ja / Nein',
          value: 'yes_or_no',
          id: 'yes_or_no',
          load: (_t, question) => {
            return this.loadPresetYesOrNo(question)
          }
        }
      ],

      // Question Formats
      question_formats: [
        {
          label: 'Slider Horizontal "slider"',
          id: 'slider',
          description: 'Slider 10er Horizontal',
          fixed_options: 1,
          icon: 'swap_horiz'
        },
        {
          label: 'Slider Vertikal "slider_vert"',
          id: 'slider_vert',
          description: 'Slider 10er Vertikal',
          fixed_options: 1,
          icon: 'swap_vert'
        },
        {
          label: 'Dropdown-Feld "dropdown"',
          id: 'dropdown',
          description: 'Dropdown Field',
          fixed_options: 1,
          icon: 'arrow_drop_down_circle'
        },
        {
          label: 'Checkboxes / Kontrollkästchen "checkboxes"',
          id: 'checkboxes',
          description: 'Checkboxes / Select-Fields',
          icon: 'check_box'
        },
        {
          label: 'Nur Text "text_only"',
          id: 'text_only',
          description: 'Nur Text "text_only"',
          icon: 'textsms'
        }
      ],

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
          align: 'center',
          sortable: true
        },
        {
          label: 'Kommentierbar',
          name: 'is_commentable',
          field: 'is_commentable',
          align: 'center',
          sortable: true
        },
        {
          label: 'Mind. Optionen',
          name: 'min_options',
          field: 'min_options',
          align: 'center',
          sortable: true
        },
        {
          label: 'Max Optionen',
          name: 'max_options',
          field: 'max_options',
          align: 'center',
          sortable: true
        },
        // {
        //   label: 'ID',
        //   name: 'id',
        //   field: 'id',
        //   align: 'center',
        //   sortable: true
        // },
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
      sOptionSearch: '',
      bDeleteOptionDialog: false,
      iOptionsPerPage: 50,
      oOptionsPagination: {
        rowsPerPage: 100,
        sortBy: 'order',
        descending: false
      },
      aOptionHeaders: [
        {
          label: 'Reihenfolge',
          name: 'order',
          field: 'order',
          align: 'left',
          sortable: true
        },
        {
          label: 'Technischer Wert',
          name: 'value',
          field: 'value',
          align: 'left',
          sortable: true
        },
        {
          label: 'Title',
          name: 'title',
          field: 'title',
          align: 'left',
          sortable: true
        },
        {
          label: 'Untertitel',
          name: 'subtitle',
          field: 'subtitle',
          align: 'left',
          sortable: true
        },
        {
          label: 'Beschreibung',
          name: 'description',
          field: 'description',
          align: 'left',
          sortable: true
        },
        {
          label: 'Farbe',
          name: 'color',
          field: 'color',
          align: 'left',
          sortable: true
        },
        // {
        //   label: 'ID',
        //   name: 'id',
        //   field: 'id',
        //   align: 'center',
        //   sortable: true
        // },
        {
          label: 'Aktionen',
          name: 'actions',
          field: 'actions',
          align: 'right'
        }
      ],

      // To Delete
      aDeleteQuestionsIds: [],
      aDeleteOptionsIds: [],

      // Options
      aSelectedOptions: [],

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
      sTodayDate: this.format_y_m_d(),
      sTodayTime: this.format_h_m_s(),
      sTodayDatetime: this.format_y_m_d_h_m_s(),

      // Datepicker Mask
      datepicker_mask: 'YYYY-MM-DD HH:mm:ss',

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
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      surveyAllowed: 'surveys/backendSurveyAllowed'
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
    } else if (typeof id === 'number') {
      this.$store.dispatch('surveys/fetchBackendSurveyAllowed', id)
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

    // // Warn before leaving
    // this.checkWarnings()
  },

  methods: {

    changeUseHtml () {
      if (!this.oSurvey.use_html) {
        this.askUseHtml()
      } else {
        this.dontUseHtml()
      }
    },

    askUseHtml () {
      this.$q.dialog({
        title: 'HTML benutzen?',
        message: 'Einverständniserklärung: Sie erklären sich damit einverstanden keinen schädlichen Code bewusst oder unbewusst in die Eingabe-Felder einzufügen.',
        persistent: true,
        options: {
          type: 'checkbox',
          model: [],
          isValid: model => model.includes('use_html'),
          items: [
            { label: 'Ich habe die oben gezeigte Einverständniserklärung gelesen, verstanden und akzeptiere diese', value: 'use_html' }
          ]
        },
        ok: {
          label: 'Einverstanden',
          color: 'warning',
          unelevated: true
        },
        cancel: {
          label: 'Abbruch',
          outline: true,
          unelevated: true
        }
      }).onOk(data => {
        this.useHtml()
      }).onCancel(data => {
        this.dontUseHtml()
      })
    },

    useHtml () {
      this.$set(this.oSurvey, 'use_html', 1)
    },

    dontUseHtml () {
      this.$set(this.oSurvey, 'use_html', 0)
    },

    tryDeleteSurvey () {
      this.$q.dialog({
        title: 'Lösche Umfrage',
        message: 'Bitte geben Sie das Wort "Löschen" ohne Anführungszeichen ein und klicken Sie auf "Unwiderruflich Löschen" um die Umfrage zu löschen.',
        prompt: {
          model: '',
          isValid: val => val === 'Löschen',
          type: 'text'
        },
        ok: {
          label: 'Unwiderruflich Löschen',
          color: 'red',
          unelevated: true
        },
        cancel: {
          label: 'Zurück',
          color: 'grey',
          unelevated: true
        }
      }).onOk(data => {
        this.$q.notify({
          message: this.$t('Umfrage wird gelöscht ...'),
          color: 'warning',
          position: 'top-right',
          actions: [{ icon: 'close', color: 'white' }],
          timeout: 6000
        })

        // Return Response
        let res = (e) => {
          this.$q.notify({
            message: this.$t('Löschvorgang abgeschlossen'),
            caption: (e),
            color: 'primary',
            position: 'top-right',
            actions: [{ icon: 'close', color: 'white' }],
            timeout: 6000
          })
          setTimeout(() => {
            this.$router.back()
          }, 250)
        }

        // Update Users
        this.$store
          .dispatch('surveys/updateSurvey', {
            survey: this.oSurvey,
            force_delete: true
          })
          .then((e) => {
            res(e)
          })
          .catch((e) => {
            res(e)
          })
      })
    },

    validJson (str) {
      try {
        JSON.parse(str)
      } catch (e) {
        return false
      }
      return true
    },

    invalidJson (str) {
      return !this.validJson(str)
    },

    toJson (str) {
      return JSON.parse(str)
    },

    importFromFile () {
      try {
        // Format Json
        const json = this.toJson(this.sImportJson)
        const sImportType = this.oImportType.value

        switch (sImportType) {
          case 'override':
            // Insert to Survey
            this.oSurvey = this.copyObject(json)
            this.afterImportSuccess()
            break

          case 'questions_append':
            // questions append
            const questions = json.questions
            for (const key in questions) {
              if (questions.hasOwnProperty(key)) {
                const q = questions[key]
                this.addQuestion(q)
              }
            }
            this.afterImportSuccess()
            break

          default:
            // Default
            this.$q.notify({
              message: 'No Valid Import-Type',
              color: 'warning',
              position: 'top-right',
              actions: [{ icon: 'close', color: 'white' }],
              timeout: 6000
            })
            break
        }
      } catch (error) {
        this.$q.notify({
          message: this.$t('Error Importing - No Valid JSON'),
          color: 'red',
          position: 'top-right',
          actions: [{ icon: 'close', color: 'white' }],
          timeout: 6000
        })
      }
    },

    afterImportSuccess () {
      // Close Dialog
      this.bImportDialog = false

      // Empty Import
      this.sImportJson = ''
    },

    exportToFile () {
      // Variables
      let data = this.copyObject(this.oSurvey)
      let filename = 'survey-' + this.format_y_m_d_h_m_s() + '.json'

      // Transform Data
      delete data.id
      delete data.created_by
      delete data.url_full
      delete data.user_finished
      delete data.created_at
      delete data.updated_at

      for (const i in data.groups) {
        if (data.groups.hasOwnProperty(i)) {
          let g = data.groups[i]
          delete g.pivot
          delete g.created_at
          delete g.updated_at
        }
      }

      // TMP Delete Groups (i dont know why im scared)
      delete data.groups

      for (const i in data.questions) {
        if (data.questions.hasOwnProperty(i)) {
          let q = data.questions[i]
          let options = q.options
          delete q.id
          delete q.survey_id
          delete q.created_by
          delete q.created_at
          delete q.updated_at

          for (const j in options) {
            if (options.hasOwnProperty(j)) {
              let o = options[j]

              delete o.id
              delete o.question_id
              delete o.created_at
              delete o.updated_at
            }
          }
        }
      }

      this.saveToFile(
        JSON.stringify(data),
        filename
      )
    },

    saveToFile (jsonData, filename) {
      let blob = new Blob([jsonData], { type: 'text/plain;charset=utf-8;' })
      if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, filename)
      } else {
        let link = document.createElement('a')
        if (link.download !== undefined) { // feature detection
          // Browsers that support HTML5 download attribute
          let url = URL.createObjectURL(blob)
          link.setAttribute('href', url)
          link.setAttribute('download', filename)
          link.style.visibility = 'hidden'
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
        }
      }
    },

    loadPresetYesOrNo (question) {
      // Change Question
      question.format = 'checkboxes'
      question.min_options = 1
      question.max_options = 1

      this.addOption({
        id: null,
        question_id: question.id,
        title: 'Nein',
        value: -1,
        color: '#cf8c36'
      }, question)

      this.addOption({
        id: null,
        question_id: question.id,
        title: 'Ja',
        value: 1,
        color: '#55a559'
      }, question)
    },

    loadPresetToThreeSlider (question) {
      // Change Question
      question.format = 'slider'
      question.min_options = 1
      question.max_options = 1

      // Change Options
      for (let i = 1; i <= 3; i++) {
        var c = '#C6C6C6'
        var v = 0
        var t = i
        var s = ''
        var d = ''

        switch (i) {
          case 1:
            v = -1
            c = '#cf8c36'
            s = 'Trifft nicht zu'
            break
          case 2:
            v = 0
            c = '#c6c6c6'
            s = 'Teilweise / Mittelmäßig'
            break
          case 3:
            v = 1
            c = '#55a559'
            s = 'Trifft zu'
            break
        }

        this.addOption({
          id: this.getRandomId(),
          question_id: question.id,
          title: t,
          value: v,
          color: c,
          subtitle: s,
          description: d
        }, question)
      }
    },

    loadPresetSchoolNotes (question, sNotes) {
      // Change Question
      question.format = 'slider'
      question.min_options = 1
      question.max_options = 1

      // Load School-Notes Preset:
      let aNotes = sNotes.split(' ')

      // Change Options
      for (const i in aNotes) {
        if (aNotes.hasOwnProperty(i)) {
          const sNote = aNotes[i]

          let c = '#C6C6C6'
          let v = 0
          let t = sNote
          let s = ''
          let d = ''

          switch (sNote) {
            case '6':
              v = -8
              c = '#cf8c36'
              s = 'ungenügend'
              break

            case '5-':
              v = -7
              c = '#cf8c36'
              s = 'knapp mangelhaft'
              break

            case '5':
              v = -6
              c = '#cf8c36'
              s = 'mangelhaft'
              break

            case '5+':
              v = -5
              c = '#cf8c36'
              s = 'voll mangelhaft'
              break

            case '4-':
              v = -4
              c = '#cfae35'
              s = 'knapp ausreichend'
              break

            case '4':
              v = -3
              c = '#cfae35'
              s = 'ausreichend'
              break
            case '4+':
              v = -2
              c = '#cfae35'
              s = 'voll ausreichend'
              break
            case '3-':
              v = -1
              c = '#c6c6c6'
              s = 'knapp befriedigend'
              break
            case '3':
              v = 1
              c = '#c6c6c6'
              s = 'befriedigend'
              break
            case '3+':
              v = 2
              c = '#c6c6c6'
              s = 'voll befriedigend'
              break
            case '2-':
              v = 3
              c = '#7ebd82'
              s = 'knapp gut'
              break
            case '2':
              v = 4
              c = '#7ebd82'
              s = 'gut'
              break
            case '2+':
              v = 5
              c = '#7ebd82'
              s = 'voll gut'
              break
            case '1-':
              v = 6
              c = '#55a559'
              s = 'knapp sehr gut'
              break
            case '1':
              v = 7
              c = '#55a559'
              s = 'sehr gut'
              break
            case '1+':
              v = 8
              c = '#55a559'
              s = 'voll sehr gut'
              break

            default:
              break
          }

          this.addOption({
            id: this.getRandomId(),
            question_id: question.id,
            title: t,
            value: v,
            color: c,
            subtitle: s,
            description: d
          }, question)
        }
      }
    },

    loadPresetToTenSlider (question, iStart) {
      // Change Question
      question.format = 'slider'
      question.min_options = 1
      question.max_options = 1

      // Change Options
      for (let i = iStart; i <= 10; i++) {
        var c = '#C6C6C6'
        var v = 0
        var t = i
        var s = ''
        var d = ''

        switch (i) {
          case 0:
            c = '#cf6035'
            s = 'Trifft überhaupt nicht zu'
            v = -5
            break

          case 1:
            c = '#cf6035'
            s = 'Trifft überhaupt nicht zu'
            v = -4
            break

          case 2:
            c = '#cf6035'
            s = 'Trifft nicht zu'
            v = -3
            break

          case 3:
            c = '#cf8c36'
            s = 'Trifft weniger zu'
            v = -2
            break

          case 4:
            s = 'Trifft weniger zu'
            v = -1
            break

          case 5:
            s = 'Unentschlossen'
            d = '50 / 50'
            v = 0
            break

          case 6:
            s = 'Trifft manchmal zu'
            v = 1
            break

          case 7:
            c = '#7ea680'
            s = 'Trifft manchmal zu'
            v = 2
            break

          case 8:
            c = '#55a559'
            s = 'Trifft eher zu'
            v = 3
            break

          case 9:
            c = '#55a559'
            s = 'Trifft absolut zu'
            v = 4
            break

          case 10:
            c = '#55a559'
            s = 'Trifft absolut zu'
            v = 5
            break

          default:
            break
        }

        this.addOption({
          id: this.getRandomId(),
          question_id: question.id,
          title: t,
          value: v,
          color: c,
          subtitle: s,
          description: d
        }, question)
      }
    },

    getNegativeColor (color) {
      if (this.isDark(color)) {
        return 'text-white'
      }
      return 'text-black'
    },

    isLight (color) {
      return this.lightOrDark(color) === 'light'
    },

    isDark (color) {
      return this.lightOrDark(color) === 'dark'
    },

    lightOrDark (color) {
      if (!color) return

      // Variables for red, green, blue values
      var r, g, b, hsp

      // Check the format of the color, HEX or RGB?
      if (color.match(/^rgb/)) {
        // If HEX --> store the red, green, blue values in separate variables
        color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/)

        r = color[1]
        g = color[2]
        b = color[3]
      } else {
        // If RGB --> Convert it to HEX: http://gist.github.com/983661
        color = +('0x' + color.slice(1).replace(color.length < 5 && /./g, '$&$&'))

        r = color >> 16
        g = color >> 8 & 255
        b = color & 255
      }

      // HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
      hsp = Math.sqrt(
        0.299 * (r * r) +
        0.587 * (g * g) +
        0.114 * (b * b)
      )

      // Using the HSP value, determine whether the color is light or dark
      if (hsp > 127.5) {
        return 'light'
      } else {
        return 'dark'
      }
    },

    askLoadPreset (question) {
      this.$q.dialog({
        title: 'Frage-Vorlage Laden',
        message: 'Vorsicht: für die Frage #' + question.id + ' "' + question.title + '" werden alle Einstellungen überschrieben',
        options: {
          type: 'radio',
          model: 'opt1',
          // inline: true
          items: this.question_presets
        },
        cancel: true,
        persistent: false
      }).onOk(selected => {
        // Get specific Presets
        var preset = this.findById(this.question_presets, selected)

        // If Preset Exists
        if (preset && preset.load) {
          // Do Basic for Presets
          this.deleteOptions(question.options)

          // Load Preset into Question
          preset.load(this, question)
        }
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },

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
        position: 'top-right',
        color: 'primary',
        timeout: 3000
      })
    },

    orderedOptions: function (options) {
      return options.sort((a, b) => (a.order > b.order ? 1 : -1))
    },

    getStartDate () {
      return this.date().format().substr(0, 10)
    },

    // getEndDate() {
    //   return this.date(this.date() + 5).format().substr(0, 10)
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

    date (d) {
      return dayjs(d)
    },

    format_y_m_d (d) {
      return this.date(d).format('YYYY-MM-DD')
    },

    format_h_m_s (d) {
      return this.date(d).format('HH:mm:ss')
    },

    format_y_m_d_h_m_s (d) {
      return this.date(d).format('YYYY-MM-DD HH:mm:ss')
    },

    events (d) {
      var entry = this.format_y_m_d(d)
      var start = this.format_y_m_d(this.oSurvey.start_datetime)
      var end = this.format_y_m_d(this.oSurvey.end_datetime)
      var today = this.format_y_m_d(this.sTodayDate)

      return (entry >= start && entry <= end) || entry === today
    },

    eventColor (d) {
      var entry = this.format_y_m_d(d)
      var start = this.format_y_m_d(this.oSurvey.start_datetime)
      var end = this.format_y_m_d(this.oSurvey.end_datetime)

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

    deleteOption (o, q) {
      this.deleteOptionBackend(o)
      this.deleteOptionFrontend(o, q)
    },

    deleteOptionBackend (option) {
      // Push into delete_questions
      this.aDeleteOptionsIds.push(option.id)
    },

    deleteOptionFrontend (option, question) {
      var pos = this.getPositionById(option, question.options) // Get Option's Position inside Question
      question.options.splice(pos, 1) // Remove from Question-Options
      this.reorderOptions(question) // Reorder Options
    },

    deleteOptions (aOptions) {
      // Vars
      var aQs = this.oSurvey.questions

      // Delete for Request
      for (const i in aOptions) {
        var o = aOptions[i]
        this.deleteOptionBackend(o)
      }

      // Delete for Frontend
      for (var i = aOptions.length - 1; i >= 0; i--) {
        var o = aOptions[i]
        var q = this.findById(aQs, o.question_id)
        this.deleteOptionFrontend(o, q)
      }

      // Empty Selected
      this.aSelectedOptions = []

      // Close Dialog
      this.bDeleteOptionDialog = false
    },

    move (oMovingElement, aList, iDir) {
      var key1 = this.getPositionByOrder(oMovingElement.order, aList)
      var key2 = key1 + iDir
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
      this.sortSelectedByOrder()
      for (var i = 0; i < this.selected.length; i++) {
        this.moveUp(this.selected[i], this.oSurvey.questions)
      }
    },

    moveSelectedDown () {
      this.sortSelectedByOrder()
      for (var i = this.selected.length - 1; i >= 0; i--) {
        this.moveDown(this.selected[i], this.oSurvey.questions)
      }
    },

    moveUp (oElem, aList) {
      console.log(oElem, aList)
      this.move(oElem, aList, -1)
    },

    moveDown (oElem, aList) {
      this.move(oElem, aList, 1)
    },

    getPositionById (oItem, oObject) {
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

    addOption (option, question) {
      var aO = question.options
      option.order = aO.length + 1
      aO.push(option)
      return option
    },

    addNewOption (question) {
      return this.addOption(
        {
          id: this.getRandomId(),
          question_id: question.id,
          color: ''
        },
        question
      )
    },

    duplicateOption (option, question) {
      if (question && option) {
        var oNewOption = this.copyObject(option)

        // Delete oNewOption.id;
        oNewOption.id = this.getRandomId()

        return this.addOption(oNewOption, question)
      }
    },

    duplicateLastOption (question) {
      var oLast = question.options[question.options.length - 1]
      this.duplicateOption(oLast, question)
    },

    // QUESTIONS
    addQuestion (q) {
      var aQ = this.oSurvey.questions

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
        options: [],
        is_commentable: 1,
        comment_is_required: 0,
        comment_is_number: 0,
        is_skippable: 0
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
        })

        return this.addQuestion(oNewQ)
      }
    },

    duplicateSelectedQuestions () {
      var aNewSelected = []
      this.selected.forEach(question => {
        aNewSelected.push(this.duplicateQuestion(question))
      })
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
        if (tab) this.active_tab = tab
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

    surveyIsValid () {
      return (
        this.oSurvey.start_datetime &&
        this.oSurvey.end_datetime &&
        this.oSurvey.title
      )
    },

    surveyIsInvalid () {
      return !this.surveyIsValid()
    },

    surveyIsEditable () {
      return (this.oSurvey.is_editable && this.bEdit) || this.bCreate || this.user.is_adminuser
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
      return this.date(ts).format('YYYY-MM-DD')
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
        if (startDate === endDate) {
          var endDatetime = this.format_y_m_d_h_m_s(this.oSurvey.end_datetime)
          var startDatetime = this.format_y_m_d_h_m_s(this.oSurvey.start_datetime)

          // SelTime
          var selTime = this.date(this.oSurvey.start_datetime).toDate()
          if (hr) selTime.setHours(hr)
          if (min) selTime.setMinutes(min)
          if (sec) selTime.setSeconds(sec)

          // Format again
          selTime = this.format_y_m_d_h_m_s(selTime)

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

      var t1 = this.date(this.oSurvey.start_datetime)
      var t2 = this.date(this.oSurvey.end_datetime)

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
          return 'negative grey'
      }
    },

    getDiffDatetimeLabel () {
      // get total seconds between the times
      var delta = this.getDiffDatetime()

      if (!delta) return ''

      // calculate (and subtract) whole days
      var iDays = Math.floor(delta / 86400)
      var sDays = ''
      delta -= iDays * 86400
      if (iDays) sDays = iDays + ' ' + this.$t('days') + ' '

      // calculate (and subtract) whole hours
      var iHours = Math.floor(delta / 3600) % 24
      var sHours = ''
      delta -= iHours * 3600
      if (iHours) sHours = iHours + ' ' + this.$t('hours') + ' '

      // calculate (and subtract) whole minutes
      var iMinutes = Math.floor(delta / 60) % 60
      var sMinutes = ''
      delta -= iMinutes * 60
      if (iMinutes) sMinutes = iMinutes + ' ' + this.$t('minutes') + ' '

      return sDays + sHours + sMinutes
    },

    isSameDay (dates) {
      if (dates && dates[0] == dates[1]) {
        return true
      }
      return false
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

    getDatesDiffDays () {
      return this.getDiffDays(
        this.oSurvey.start_datetime,
        this.oSurvey.end_datetime
      )
    },

    getDiffDays (d1, d2) {
      var date1 = new Date(d1)
      var date2 = new Date(d2)
      var diffInTime = date2.getTime() - date1.getTime()
      var diffInDays = diffInTime / (1000 * 3600 * 24)

      return (Math.abs(diffInDays) + 1).toString()
    },

    getDateFormat (sDate) {
      var locale = ''
      var dDate = new Date(sDate)

      switch (this._i18n.locale) {
        case 'de':
          locale = 'de-DE'
          break

        default:
          locale = this._i18n.fallbackLocale
          break
      }

      if (sDate && dDate) {
        return dDate.toLocaleDateString(locale)
      }
    },

    getTimeFormat (sDate) {
      return sDate ? sDate.substr(11, 5) + ' Uhr' : ''
    },

    showSnackbarError (text) {
      this.$q.notify({
        message: this.$t(text),
        color: 'red',
        position: 'top-right',
        actions: [{ icon: 'close', color: 'white' }],
        timeout: 6000
      })
    },

    showSnackbarSuccess (text) {
      this.$q.notify({
        message: this.$t(text),
        color: 'green',
        position: 'top-right',
        actions: [{ icon: 'close', color: 'white' }],
        timeout: 3000
      })
    },

    startEditMode: function () {
      this.bCreate = false
      this.bEdit = true
    },

    startCreateMode: function () {
      this.bCreate = true
      this.bEdit = false

      var tmp = {
        active: 1,
        only_editable_by_creator: 1,
        is_public: 0,
        use_html: 0,
        questions: []
      }

      if (!this.oSurvey) this.oSurvey = this.copyObject(tmp)
      if (!this.oSurveyOld) this.oSurveyOld = this.copyObject(tmp)
    },

    copyObject (obj) {
      if (typeof obj !== 'undefined') {
        var copy = JSON.parse(JSON.stringify(obj))
        if (copy) return copy
      }
    },

    isSaved () {
      var bReturn = true
      var itemL = this.copyObject(this.oSurvey)
      var itemR = this.copyObject(this.oSurveyOld)

      if (itemL && itemR) {
        // Differences
        if (JSON.stringify(itemR) !== JSON.stringify(itemL)) {
          // console.log(JSON.stringify(itemR), JSON.stringify(itemL));
          bReturn = false
        } else {
          bReturn = true
        }
      }
      return bReturn
    },

    isUnsaved () {
      return !this.isSaved()
    },

    resetSurvey: function () {
      this.$q.dialog({
        title: 'Umfrage zurücksetzen',
        message: 'Umfrage wirklich zurücksetzen?',
        ok: {
          label: 'Zurücksetzen',
          color: 'warning',
          depressed: true
        },
        cancel: {
          label: 'Abbruch'
        },
        persistent: true
      }).onOk(() => {
        this.oSurvey = this.copyObject(this.oSurveyOld)
      })
    },

    tryUpdateSurvey: function () {
      if (
        this.oSurvey.start_datetime <= this.format_y_m_d_h_m_s() &&
        this.oSurvey.active &&
        !this.user.is_adminuser
      ) {
        this.$q.dialog({
          html: true,
          title: 'Warnung!',
          message: 'Wenn sie diese Umfrage speichern, ist diese im Anschluss <strong>nichtmehr</strong> bearbeitbar. ' +
                    '<br><br>Dies hat Zwei Gründe: <br>1. Die Umfrage ist als <strong>"Aktiviert"</strong> markiert <br>2. <strong>Das Startdatum</strong> der Umfrage liegt in der Vergangenheit,',
          ok: {
            label: 'Trotzdem Speichern',
            color: 'red',
            depressed: true
          },
          cancel: {
            label: 'Zurück',
            depressed: true
          },
          persistent: true
        }).onOk(() => {
          this.updateSurvey()
        })
      } else {
        this.updateSurvey()
      }
    },

    updateSurvey () {
      // Loader
      this.$q.loading.show({
        message: 'Updating Survey',
        delay: 400
      })

      // Update Users
      this.$store
        .dispatch('surveys/updateSurvey', {
          survey: this.oSurvey,
          delete_questions_ids: this.aDeleteQuestionsIds,
          delete_options_ids: this.aDeleteOptionsIds
        })
        .then((e) => {
          // Success
          if (!e || !e.response || !e.response.data || !e.response.data.error) {
            this.showSnackbarSuccess(this.$t('data_saved'))
            this.oSurvey = this.copyObject(e.data)
            this.oSurveyOld = this.copyObject(e.data)

            if (this.bCreate) {
              this.$router.push({
                name: 'backend.survey',
                params: {
                  id: this.oSurvey.id
                }
              })
            }
            this.aDeleteQuestionsIds = []
            this.aDeleteOptionsIds = []
            this.startEditMode()
          }
          this.$q.loading.hide()
        })
        .catch((e) => {
          console.log(e.response.data)
          console.log(e.response.data.errors)
          // Error
          let errText = ''
          if (e.response && e.response.data && e.response.data) {
            let err = e.response.data.errors
            for (var i in err) {
              errText += ': ' + err[i]
            }
          }
          this.showSnackbarError(this.$t('data_unsaved') + '\n' + errText)
          this.$q.loading.hide()
        })
    }

  }
}
</script>
